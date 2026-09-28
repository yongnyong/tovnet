"""운영 접속 없이 합성 값으로 공개 로직의 주요 경계를 확인합니다."""
from datetime import datetime, timedelta
import math

from floating import confirmed_level, floating_level, to_percent
from risk import FEATURE_NAMES, TreeModel, features, make_minute_series


def main():
    assert to_percent(0.5) == 50.0
    assert floating_level(24.9) is None
    assert floating_level(25) == "warn"
    assert floating_level(50) == "alert"
    assert floating_level(75) == "danger"
    assert confirmed_level({"warn": 3, "danger": 2}, 3) == "warn"
    assert confirmed_level({"danger": 3}, 3) == "danger"

    now = datetime(2026, 1, 1, 12, 30)
    rows = [(now - timedelta(minutes=30-i), float(i)) for i in range(31)]
    series = make_minute_series(rows, now)
    assert series == [float(i) for i in range(31)]
    x = features(series, now.hour, now.weekday(), 0)
    assert len(x) == len(FEATURE_NAMES)
    assert x[FEATURE_NAMES.index("slope30")] == 1.0
    # 5분 결측은 직전 값으로 보완하고 6분 결측에서는 계산을 중단합니다.
    assert make_minute_series(rows[:-5], now) is not None
    assert make_minute_series(rows[:-6], now) is None
    assert make_minute_series(rows[1:], now) is None

    # 실제 모델 대신 분기 확인을 위한 합성 트리를 사용합니다.
    dump = {
        "feature_names": FEATURE_NAMES,
        "tree_info": [{"tree_structure": {
            "split_feature": 0, "threshold": 25.0, "default_left": True,
            "left_child": {"leaf_value": -1.0},
            "right_child": {"leaf_value": 1.0},
        }}],
    }
    model = TreeModel(dump)
    assert math.isclose(model.predict(x), 1 / (1 + math.exp(-1)))
    assert TreeModel(dump, task="regression").predict(x) == 1.0
    try:
        TreeModel({"feature_names": [], "tree_info": []})
    except ValueError:
        pass
    else:
        raise AssertionError("Feature order mismatch must be rejected")
    print("PASS: synthetic stage, missing-data, feature-order and tree checks")
    print("Synthetic current ratio:", series[-1])


if __name__ == "__main__":
    main()
