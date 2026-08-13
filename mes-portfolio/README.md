# PHP 기반 MES · 업무관리 · 고객 A/S 통합 플랫폼

> 제조 도메인 전문가와 협업하여 생산·재고·시리얼 번호 관리부터 사내 업무공유, 주간보고, 고객 A/S까지 연결하는 웹 기반 MES를 개발하고 있습니다.

## Portfolio Files
- `MES_업무관리_포트폴리오_양호용.pptx` — 발표용 포트폴리오
- `PROJECT_SUMMARY.md` — 프로젝트 핵심 요약
- `PRESENTATION_SCRIPT.md` — 발표·면접용 설명 스크립트
- `assets/` — 공개 가능한 화면 자료를 추가하는 폴더

## Project Overview
- 상태: 개발 진행 중
- 개발 형태: 사수와 2인 협업
- 기술: PHP, MySQL, HTML, CSS, JavaScript, Bootstrap 5
- 협업: Git, GitHub

## Architecture
`Browser → PHP Web Application → PDO/MySQL`

- Frontend: Bootstrap 5, JavaScript, Fetch API
- Backend: PHP, Session
- Database: MySQL, PDO, Prepared Statement
- Security/Data Integrity: 비밀번호 해시, CSRF, Transaction, Row Lock

## Key Modules
- 사용자·부서·권한·감사 로그
- 제품·부품·BOM·소모품 기준정보
- 창고·재고·완제품·생산·불량 관리
- 시리얼 번호 조회 및 이력 추적
- MAIN/SUB 업무, 댓글, 첨부파일, 진척도
- 주간보고 작성과 업무 연결
- 고객·판매·A/S 관리
- 칸반 기반 프로젝트 관리

## Contribution Boundary
| 구분 | 역할 |
|---|---|
| 사수 주도 | 제조업 경험 기반 공장 납품, 시리얼 번호, 생산·재고 프로세스 |
| 양호용 중심 | 업무공유 프로세스, 주간보고 연결, 고객 A/S 흐름 |
| 공동 개발 | 프로젝트 관리 기능, 기능 우선순위 협의, Git 기반 통합 |

코드에 작성자 표기가 없으므로 개인 기여는 확인된 역할 범위만 기술하며 개별 파일의 단독 소유권은 주장하지 않습니다.

## Current Status
주요 화면과 핵심 로직을 구현하며 권한별 사용성, 입력 흐름, 데이터 연결, 예외 처리와 현장 피드백을 반영하고 있습니다.

## Public Repository Notice
공개 저장소에는 실제 고객명, 사용자명, IP, 연락처, DB 접속정보와 내부 업무 데이터가 포함된 화면 또는 설정 파일을 올리지 않습니다.
