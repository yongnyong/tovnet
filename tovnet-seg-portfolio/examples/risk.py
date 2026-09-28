"""시계열 특성 계산 및 LightGBM JSON 트리 평가의 공개 참조 구현.

실제 개발 코드에서 추출했습니다. 운영 모델, 카메라별 메타데이터 및 측정값은
포함하지 않습니다. 입력·모델 형식에 제약이 있는 프로젝트용 구현이며,
범용 LightGBM 런타임이나 검증된 현장 예측기를 의미하지 않습니다.
"""

import json
import math
import os
from datetime import timedelta

WINDOW_MIN = 30  # 특성에 쓰는 과거 길이(분). 시계열 길이는 WINDOW_MIN + 1
LAGS = (1, 2, 3, 5, 10, 15, 20, 30)
ROLLS = (5, 10, 30)
FILL_LIMIT = 5  # 결측 분을 앞 값으로 채우는 최대 길이(학습 데이터 전처리와 동일)

HORIZON_MIN = 30  # 예측 지평(분) — sweep_*30 특성이 보는 미래 시점
SWEEP_NAMES = ["sweep_sin", "sweep_cos", "sweep_sin30", "sweep_cos30"]

FEATURE_NAMES = (
    ["cur"]
    + [f"lag{k}" for k in LAGS]
    + [n for w in ROLLS for n in (f"mean{w}", f"max{w}", f"min{w}", f"std{w}", f"slope{w}")]
    + ["hour", "dow", "stream_id"]
    + SWEEP_NAMES
)


def sweep_phase(sweep, stream_id, now):
    """PTZ 왕복 스캔 위상 특성 — [sin, cos(지금), sin, cos(HORIZON_MIN 뒤)].

    sweep: {"<stream_id>": {"period_min": P, "t0": "YYYY-MM-DD HH:MM:SS", "valid_from": ...}}
    (experiments/forecast/fit_sweep_period.py 가 만든 것, 모델 JSON 의 meta.sweep 에 같이 실려 온다).
    카메라가 일정한 속력으로 왕복하면 화면은 시각의 결정적 함수라, 미래 30분의 위상도 미리 안다.
    주기 정보가 없는 카메라(고정 카메라)나 valid_from 이전 시각은 0 — "위상 모름".
    now: 현재 분(naive KST datetime).
    """
    info = (sweep or {}).get(str(stream_id))
    if not info or now is None:
        return [0.0, 0.0, 0.0, 0.0]
    from datetime import datetime
    t0 = datetime.fromisoformat(str(info["t0"]))
    now = now.replace(tzinfo=None)
    if info.get("valid_from") and now < datetime.fromisoformat(str(info["valid_from"])):
        return [0.0, 0.0, 0.0, 0.0]
    P = float(info["period_min"])
    out = []
    for offset in (0, HORIZON_MIN):
        ph = (((now - t0).total_seconds() / 60.0 + offset) % P) / P
        out += [math.sin(2 * math.pi * ph), math.cos(2 * math.pi * ph)]
    return out


def features(vals, hour, dow, stream_id, now=None, sweep=None):
    """vals: 길이 31 리스트(인덱스 30 = 현재 분). 결측(None) 이 있으면 None 을 돌려준다.

    pandas 로 학습할 때와 같은 정의: rolling(w) 는 현재 포함 w 개, std 는 표본표준편차(ddof=1),
    slope_w = (현재 - w분 전) / w. now·sweep 을 주면 스캔 위상 특성을 계산하고, 없으면 0.
    """
    if len(vals) != WINDOW_MIN + 1 or any(v is None for v in vals):
        return None
    cur = vals[-1]
    out = [cur]
    for k in LAGS:
        out.append(vals[-1 - k])
    for w in ROLLS:
        win = vals[-w:]
        m = sum(win) / w
        var = sum((x - m) ** 2 for x in win) / (w - 1)
        out += [m, max(win), min(win), math.sqrt(var), (cur - vals[-1 - w]) / w]
    out += [hour, dow, stream_id]
    out += sweep_phase(sweep, stream_id, now)
    return out


def make_minute_series(rows, now_minute):
    """(측정시각, 비율) 행들 -> 최근 31분의 1분 평균 리스트. 결측은 FILL_LIMIT 까지 앞 값으로.

    now_minute: 현재 분(초 0). rows 는 [now-30분, now] 범위를 덮어야 한다.
    채워도 남는 결측이 있으면 None 을 돌려준다(특성 계산 불가).
    """
    buckets = {}
    for ts, ratio in rows:
        key = ts.replace(second=0, microsecond=0)
        buckets.setdefault(key, []).append(ratio)
    vals = []
    last, gap = None, 0
    for i in range(WINDOW_MIN, -1, -1):
        key = now_minute - timedelta(minutes=i)
        if key in buckets:
            last = sum(buckets[key]) / len(buckets[key])
            gap = 0
            vals.append(last)
        elif last is not None and gap < FILL_LIMIT:
            gap += 1
            vals.append(last)
        else:
            return None
    return vals


class TreeModel:
    """LightGBM Booster.dump_model() JSON 을 읽어 예측한다.

    meta.task 가 "binary"(기본)면 확률(시그모이드), "regression"이면 잎 값 합이 곧 예측값.
    """

    def __init__(self, dump, task="binary"):
        self.trees = [t["tree_structure"] for t in dump["tree_info"]]
        self.feature_names = list(dump["feature_names"])
        self.task = task
        if self.feature_names != FEATURE_NAMES:
            raise ValueError("모델의 특성 순서가 risk.FEATURE_NAMES 와 다르다 — 재학습 필요")

    @classmethod
    def load(cls, path):
        with open(path, encoding="utf-8") as f:
            obj = json.load(f)
        meta = obj.get("meta", {})
        m = cls(obj["model"], task=meta.get("task", "binary"))
        m.meta = meta
        m.sweep = meta.get("sweep", {})  # 카메라별 스캔 주기 — 학습 때 쓴 값을 그대로 운영에서 씀
        return m

    def predict(self, x):
        """회귀: 예측값(비율 %). 분류: 확률(0~1)."""
        raw = sum(self._walk(t, x) for t in self.trees)
        if self.task == "regression":
            return raw
        return 1.0 / (1.0 + math.exp(-raw))

    @staticmethod
    def _walk(node, x):
        while "leaf_value" not in node:
            v = x[node["split_feature"]]
            # decision_type 은 학습 특성이 모두 수치라 "<=" 만 나온다.
            go_left = v <= node["threshold"] if v == v else node.get("default_left", True)
            node = node["left_child"] if go_left else node["right_child"]
        return node["leaf_value"]

    def predict_proba(self, x):
        raw = sum(self._walk(t, x) for t in self.trees)
        return 1.0 / (1.0 + math.exp(-raw))


_MODELS = {}
_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(_DIR, "risk_model.json")  # 30분 내 경계(50%) 도달 확률
FORECAST_PATH = os.path.join(_DIR, "forecast_model.json")  # 30분 뒤 예상 부유물 면적(%)


def _load(path):
    if path not in _MODELS and os.path.exists(path):
        _MODELS[path] = TreeModel.load(path)
    return _MODELS.get(path)


def get_model():
    """경계 확률 모델(운영용 싱글턴). 파일이 없으면 None(게이지 미표시)."""
    return _load(MODEL_PATH)


def get_forecast_model():
    """30분 뒤 예상 면적 회귀 모델. 파일이 없으면 None."""
    return _load(FORECAST_PATH)
