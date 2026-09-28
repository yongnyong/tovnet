"""부유물 비율(%) 단계 판정 — api.views(이벤트 생성)와 cctv.views(화면 표시)가 공유.

순환 import(notification.models 가 cctv.models 를 import)를 피하려고 Level enum 대신
단계 코드 문자열을 쓴다. 코드 값은 notification.models.Level 의 값과 반드시 일치해야 한다.
"""

# 높은 단계부터, "값 >= 기준" 으로 판정.
FLOATING_TIERS = ((75, "danger"), (50, "alert"), (25, "warn"))
# 단계 심각도 순위(상승 비교용).
LEVEL_RANK = {"warn": 1, "alert": 2, "danger": 3}


def to_percent(ratio):
    """외부 계약상 0~1 비율 -> 표시/판정용 퍼센트(소수 1자리). None은 그대로."""
    return None if ratio is None else round(ratio * 100, 1)


def floating_level(ratio):
    """부유물 비율(퍼센트) -> 단계 코드, 정상 구간(또는 값 없음)이면 None."""
    if ratio is None:
        return None
    for threshold, level in FLOATING_TIERS:
        if ratio >= threshold:
            return level
    return None


def confirmed_level(band_counts, n):
    """단계별(밴드) 카운트에서, 그 단계 이상 누적이 n 회 이상인 최고 단계 코드.

    band_counts 는 각 측정을 floating_level 밴드 1개에만 센 값. 높은 단계부터
    누적해 처음 n 에 도달한 단계를 반환한다(없으면 None). 예) {warn:3,danger:2}
    는 danger 누적 2 < n=3, warn 누적 5 >= 3 → "warn".
    """
    cumulative = 0
    for _threshold, level in FLOATING_TIERS:  # 높은 단계부터 (danger→alert→warn)
        cumulative += band_counts.get(level, 0)
        if cumulative >= n:
            return level
    return None
