# TOVNET | Field R&D & Development Portfolio

토브넷 업무에서 수행한 웹 시스템 개발, CCTV 기반 Vision AI, 실시간 모니터링 및 현장 네트워크 경험을 정리합니다. 프로젝트의 구현 범위, 문제 해결 과정과 남은 검증 항목을 함께 기록합니다.

> **2026-09-29 업데이트** — MES는 2026-08-26 개발본, AI·탄천 플랫폼은 2026-09-28 개발본의 코드와 기록을 기준으로 정리했습니다. 코드 존재와 실제 운영 검증은 구분합니다. [업데이트 내역과 확인 범위](docs/UPDATE_2026-09-29.md)

## Projects

| 프로젝트 | 공개 내용 | 기술 |
|---|---|---|
| [MES 업무관리 플랫폼](mes-portfolio/) | 제조·업무공유·A/S에서 영업 계약, SLA, RMA, 유심·메일 관리로 확장된 기능 | PHP 8, PDO, MariaDB, JavaScript, 자체 CSS/JS |
| [TOVNET-SEG](tovnet-seg-portfolio/) | 기술 인수, Full Resolution 추론, 모듈화, 재학습 모델 비교와 예측 실험 | Python, PyTorch, OneFormer, Swin-Large, FFmpeg |
| [탄천 AI·센서 통합 모니터링](tancheon-monitoring-portfolio/) | CCTV HLS, AI 부유물 비율, 센서 수집, SSE, 통계·알림 및 예측값 연동 | Django, Redis, MySQL, MediaMTX, FastAPI, Docker Compose |
| [탄천 CCTV Network](tancheon-cctv-network-portfolio/tancheon-cctv-network-portfolio/) | 현장 영상 접속 장애 분석과 IP·포트포워딩 운영 절차 | RTSP, NAT, DHCP, Omada, ER605 |
| [USIM Management](usim-management-portfolio/) | 기존 Laravel 기반 유심 관리 포트폴리오와 공개 코드 | Laravel, PHP, MySQL, Bootstrap, Excel |

## MES · 업무관리 플랫폼

MAIN/SUB 업무, 주간보고, 고객 A/S, 프로젝트 칸반을 제조·재고 데이터와 연결합니다. 최신 개발본에는 거래처 → 계약 → 영업단계 구조, 유지보수 SLA, RMA 반품·교환, 출하·견적, 유심 통합 관리, POP3 메일 수집 코드가 포함됩니다.

최신 MES 화면은 프레임워크 없는 HTML/CSS/JavaScript와 자체 모달을 사용하는 구조로 정리했습니다. 기존 Laravel USIM 프로젝트와 MES 내부 유심 모듈은 별도 구현입니다. 개인 역할과 공동 개발 범위는 [MES 문서](mes-portfolio/README.md)에 구분했습니다.

## AI 분석에서 모니터링 플랫폼까지

RTSP 프레임 수집 → OneFormer 추론 → 부유물 마스크·비율 계산 → 웹 플랫폼 저장·표시·이벤트로 이어지는 구조입니다. AI 서버는 수집·추론·이벤트·출력·제어 모듈로 나뉘고, 플랫폼은 센서 및 AI 상태를 Redis와 SSE로 브라우저에 전달합니다.

- **영상·AI:** 원본/AI HLS 재생 경로, 부유물 비율 측정과 단계 판정
- **센서:** RadioNode 게이트웨이의 측정값 정규화·전달, 중복 저장 방지, 실시간 화면 갱신
- **알림:** 반복 측정을 누적해 단계 확정, 재알림 간격 제어, 알림 피드·문자 발송 대기열
- **예측:** 최근 측정값으로 30분 내 경계 도달 확률과 30분 뒤 예상 면적을 계산하는 플랫폼 코드
- **실험:** 재학습 모델 비교, 예측 모델 평가. 실험 결과를 운영 배포 완료나 현장 정확도 보증으로 표현하지 않습니다.

[통합 모니터링 구조와 구현 상태](tancheon-monitoring-portfolio/README.md) · [재학습 비교 결과와 한계](tovnet-seg-portfolio/EVALUATION.md) · [실제 코드에서 선별한 공개 예제](tancheon-monitoring-portfolio/examples/README.md)

## 공개 범위

기능·구조·개발 경과, 집계된 평가 결과, 접속정보 없는 선별 코드와 합성 데이터 예제를 공개합니다. 운영 설정, 계정·토큰, 고객·업무 원자료, 장치 식별정보, 현장 원본 영상, 학습 데이터와 모델 가중치는 이번 업데이트에 포함하지 않습니다.

기존 PDF·PPTX·화면 이미지는 이전 시점의 발표·구현 기록으로 보존합니다. 최신 상태는 각 README와 업데이트 문서를 기준으로 보며, 기존 바이너리 자료 전체를 이번에 재검수한 것은 아닙니다.

## 기여 범위

MES는 기존에 구분한 개인 중심 업무·사수 주도 제조 영역·공동 개발 범위를 유지합니다. AI 원개발 연구진과 인수 이후의 프로젝트 확장도 구분합니다. 이번에 확인한 최신 기능 전체를 개인 단독 개발 성과로 주장하지 않습니다.
