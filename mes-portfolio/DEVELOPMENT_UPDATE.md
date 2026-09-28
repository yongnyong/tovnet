# 최신 v2 코드 기준 구현 지도

**확인일: 2026-09-29.** 사용자가 내려받은 v2 소스를 기준으로 기존 Git 개발본(2026-08-26)과 비교했습니다.

최상위 PHP 94개 중 기존 Git에 없던 파일 32개, 줄바꿈만의 차이를 제외해 내용이 달라진 파일 33개, 줄바꿈만 달라진 파일 29개를 확인했습니다. 파일 개수는 기능 완성률이 아니며, 다운로드 타임스탬프를 개발 날짜로 사용하지 않습니다.

## 이전 설명에서 빠졌던 변화

| 영역 | 이전 공개 설명 | 최신 v2에서 확인한 변화 |
|---|---|---|
| 영업 | 거래처·계약·단계·메모 | 국내/해외 공통 로직, 품목·유심·S/N, 견적·계약·납품, 수금·계산서·현장처리 연결 |
| 생산 | 작업지시·조립·완제품 재고 | 영업에서 생산요청, 완료 시 BOM 차감·완제품 증가·S/N 등록, 취소 처리 |
| 기준정보 | 제품·부품별 마스터 | 통합 품목·옵션, 반제품과 중첩 BOM |
| 재고 | 창고 단위 재고 | 캐비닛·칸 위치 추가, 이전 재고의 임시 위치 이관 |
| 유심 | 고객·장치·상태 | 설치 이력, 복원, 대여·반납, 완제품 S/N·영업 연결 |
| 메일·협업 | 메일함·POP3 수집 | MIME·첨부·분류 규칙, 업무협조 전환, 화면 → Task 등록 |
| 회의·현황 | 주간보고·경영 현황 | 영업회의 취합·인쇄, 영업/생산/연구소/회계 대시보드 |

## 소스와 기능의 대응

아래 파일명은 구현 근거이며 원본 PHP 전체를 공개 배포했다는 뜻은 아닙니다.

| 소스 | 확인한 코드 경로 |
|---|---|
| `sales_log.php`, `overseas_sales_log.php` | 공통 영업 로직·데이터 영역 분리, 단계/체크리스트/메모, 계약 품목, 생산요청·서비스 연결 |
| `sales_log.php::createDocPlan` | 일시불·분할 입금 계획, 계산서 금액과 분할 합계 확인 |
| `sales_log.php::RECEIVE_DEAL_PAYMENT` | 같은 예정 항목에 실제 입금액 누적 |
| `sales_log.php::CANCEL_DEAL_DOCUMENT` | 문서 취소 시 미입금 계획 정리·기입금 기록 유지 |
| `contract_form.php`, `contract_lib.php`, `contract_print.php` | 견적 품목 가져오기, 조항/특약 편집·템플릿, 토큰 치환, 인쇄·Word 출력 |
| `quote_print.php` | 견적 출력 |
| `invoice_status.php` | 국내·해외의 계산서 예정·발행 미수·미입금 일정 통합 조회 |
| `service_admin.php`, `service_lib.php` | 배송/설치/대여, 상태·담당자·시리얼, 설치 완료 → 유심 정보 갱신 |
| `production_request.php`, `production_request_lib.php` | 요청·수량·납기·옵션, 생산완료의 BOM/재고/S/N 처리, 완료 취소·기존 S/N 연결 |
| `production_pop.php` | 생산 현황 표시 화면 |
| `item_master.php`, `option_lib.php` | 통합 품목·완제품·옵션 관리 |
| `semi_bom_master.php`, `semi_lib.php` | 반제품 구성과 직접·간접 순환참조 검사 |
| `semi_assembly_registration.php`, `assembly_registration.php` | 반제품·완제품 조립과 구성품 재고 반영 |
| `inventory_lib.php`, `stock_admin.php`, `part_stock_admin.php` | 창고/캐비닛/칸, 위치별 재고와 입출고·이동, 구 재고 이관 |
| `sn_search.php` | 생산요청·제품·BOM·유심과 이어지는 S/N 조회 |
| `usim_admin.php` | 배치 이력·복원, 대여·반납·예정일, 장치와 완제품 S/N 연결 |
| `mail_sync_lib.php`, `mail_rules.php` | MIME 본문·첨부 해석, POP3 수집·분류 규칙 |
| `mail_detail.php`, `mail_write.php` | 메일을 업무협조로 전달, 첨부 연결, 편집·임시저장 |
| `work_order.php`, `work_order_lib.php`, `work_order_log.php` | 업무협조 상태와 완료·삭제 이력 |
| `meeting_pin.php`, `sidebar.php` | 화면 항목을 Task로 등록하고 원래 화면 링크 연결 |
| `weekly_sales_meeting.php` | 각 업무 현황 자동 취합 + 담당자의 주간 현황·계획 + 인쇄 |
| `dashboard_sales.php`, `dashboard_production.php`, `dashboard_rnd.php`, `dashboard_accounting.php` | 부서별 업무 데이터 집계 |
| `my_memo.php` | 개인 메모·고정·수정 |

## 기술 설명 수정

이전 수정에서는 개발 README의 “자체 UI” 설명만으로 Bootstrap을 제외했습니다. **최신 실제 파일에는 두 방식이 함께 있습니다.** 메일·소모품 등 일부 화면은 Bootstrap 5를 로드하고, 다른 화면은 자체 CSS/JS·모달을 사용합니다. 캘린더에는 FullCalendar, 날짜 입력에는 flatpickr가 확인됩니다.

## 현재 구조의 경계

- 위치 재고와 창고 합계 재고가 공존합니다. 생산요청 완료 경로는 합계 재고를 사용하므로 위치까지 일관되게 반영되는지 별도 확인이 필요합니다.
- 생산완료 취소 시 창고 정보가 없는 과거 기록은 일반 취소와 처리 경로가 다릅니다. 완전한 복구 보장을 주장하지 않습니다.
- 계산서 기능은 등록·예정·입금·미수 관리이며 외부 전자발행 시스템을 호출하는 기능으로 설명하지 않습니다.
- 부서별 화면의 쿼리·처리 경로를 확인했습니다. 실제 데이터로 전 기능 실행 시험을 수행한 것은 아닙니다.
- 보안 설정·회사 정보·사용자 및 고객 실명이 들어 있는 원본은 공개용으로 복사하지 않았습니다.

[포트폴리오로 돌아가기](README.md)
