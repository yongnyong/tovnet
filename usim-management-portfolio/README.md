# 유심(USIM) 관리 시스템

통신 유심(SIM)의 계약 → 일시정지 → 해지 흐름과 고객·기기 정보를 한 곳에서 관리하는
웹 애플리케이션입니다. 제가 운영 중인 **토브넷**에서 실제로 유심 재고와 계약 상태를
엑셀로 관리하다가 한계에 부딪혀서, 직접 설계·개발부터 실데이터 이관까지 진행한
프로젝트입니다. MES(제조실행시스템)처럼 자산 하나하나의 현재 상태와 변경 이력을
끝까지 추적하는 것을 목표로 잡았습니다.

## 배경

현장에서 유심을 대량으로 운용하다 보면 "이 유심 지금 누구 거지?", "언제 해지했지?",
"재발급은 몇 번째지?" 같은 질문에 엑셀만으로는 답하기 점점 어려워집니다. 특히 상태가
바뀔 때마다 셀을 덮어써버리면 "예전에 무슨 일이 있었는지"가 사라진다는 게 가장 큰
문제였습니다. 이 프로젝트는 그 이력을 데이터베이스 레벨에서 append-only로 보존하는
구조로 풀었습니다.

## 주요 기능

- **고객 / 유심 / 기기 CRUD** — 검색, 필터, 페이지네이션
- **상태 이력 자동 기록** — 유심 상태(사용중/일시정지/해지)가 바뀔 때마다 `UsimObserver`가
  누가·언제·무엇에서 무엇으로 바뀌었는지 `usim_status_histories` 테이블에 자동으로 남김
- **권한 분리** — 관리자/직원 역할 기반 접근 제어 (사용자 관리 메뉴는 관리자 전용, 미들웨어로 403 처리)
- **엑셀 업로드/다운로드** — 유심번호를 키로 upsert(신규 등록 또는 갱신), 존재하지 않는
  고객·기기는 자동 생성. `maatwebsite/excel` 기반
- **레거시 데이터 이관 커맨드** — 실제 운영 중이던 엑셀 자료(다중 시트, 병합 컬럼, 자유
  텍스트 메모 등 전형적인 "현장 엑셀")를 이관하기 위한 전용 artisan 커맨드 (`usim:import-legacy`)
- **대시보드** — 상태별 건수, 최근 상태 변경 이력

## 기술 스택

| 영역 | 사용 기술 |
|---|---|
| Backend | Laravel 9 (PHP 8.0+) |
| DB | MySQL / MariaDB |
| Frontend | Blade + Bootstrap 5 (별도 빌드 도구 없이 CDN으로 구성) |
| Excel | maatwebsite/excel (PhpSpreadsheet) |
| Auth | Laravel 세션 기반 인증 + 커스텀 역할 미들웨어 |

프론트엔드 빌드 파이프라인(Node/Vite) 없이 순수 Blade + Bootstrap CDN으로 구성해서,
PHP와 MySQL만 있으면 어떤 환경(사내 서버 포함)에서도 바로 구동되도록 만들었습니다.

## 기술적 의사결정 & 트러블슈팅

실제로 겪은 문제와 판단 과정을 정리했습니다.

- **엑셀 헤더가 한글이면 업로드가 조용히 깨지는 문제**
  `maatwebsite/excel`의 기본 헤더 포맷터(`slug`)는 영문 트랜스리터레이션 기반이라
  "유심번호" 같은 한글 헤더를 빈 문자열로 만들어버립니다. 에러 없이 데이터가 전부
  `null`로 들어가는 조용한 실패였는데, `config/excel.php`의 `heading_row.formatter`를
  `none`으로 바꿔서 원본 헤더 문자열을 그대로 배열 키로 쓰도록 해결했습니다.

- **상태값 이름을 나중에 바꿔야 했을 때 (계약 → 사용중)**
  운영 중 이미 데이터가 쌓인 `ENUM` 컬럼의 값 하나를 바꿔야 했습니다. 그냥 `ENUM`을
  덮어쓰면 기존 값과 충돌하므로, ① enum을 신규/구 값 모두 허용하도록 넓히고 → ② 데이터를
  일괄 변환하고 → ③ enum을 최종 값으로 좁히는 3단계 마이그레이션으로 무중단 처리했습니다.

- **레거시 엑셀 → 정규화된 이력 데이터로 재구성**
  기존 엑셀에는 "현재 상태"만 있고 "언제 상태가 바뀌었는지"의 이력이 없었습니다.
  이관 스크립트에서는 개통일/일시정지일/해지 여부 컬럼을 조합해서 계약 → (일시정지) →
  (해지)의 시간순 이력 체인을 직접 재구성해 넣었습니다. 이 과정은 실시간 `UsimObserver`가
  아니라 `withoutEvents()`로 우회해서, 과거 시점의 이력을 실제 발생 순서대로 넣을 수
  있게 만들었습니다.

- **개발 PC의 PHP가 8.0(EOL)이라 최신 Laravel 패치를 못 받는 문제**
  Composer 2.10+의 보안 어드바이저리 차단 기능 때문에 `laravel/framework`의 최신 9.x
  패치 버전 설치가 막혔습니다. 로컬 개발은 `composer config --global
  policy.advisories.block false`로 우회했지만, 실서버는 PHP 8.1+로 올려서 이 우회 없이
  패치된 버전을 쓰는 걸 원칙으로 잡았습니다.

- **OneDrive 동기화 폴더를 피해서 배치**
  `vendor` 폴더처럼 파일 수천 개짜리 디렉터리를 OneDrive 동기화 폴더 안에 두면 빌드/설치
  중 파일 잠금 충돌이 잦습니다. 그래서 프로젝트는 XAMPP의 `htdocs` 밑에 두고, 개인 문서
  폴더와는 분리했습니다.

## 데이터 모델

```
customers 1---N usims N---1 devices
                 |
                 └---N usim_status_histories N---1 users (changed_by)
```

- `usims.status`는 `사용중 / 일시정지 / 해지` 3가지 상태를 가지며, 상태가 바뀔 때마다
  `usim_status_histories`에 새 레코드가 쌓입니다 (수정이 아니라 append-only 이력).
- 유심 1개는 기기 1개에만 연결될 수 있도록 `device_id`에 유니크 제약을 걸었습니다.

## 화면 구성

- 로그인
- 대시보드 (상태별 통계, 최근 이력)
- 유심 목록/등록/수정/상세 (상세 페이지에 상태 변경 타임라인 표시)
- 고객 목록/등록/수정/상세
- 기기 목록/등록/수정
- 사용자 관리 (관리자 전용)
- 유심 엑셀 업로드/다운로드

## 시작하기

```bash
git clone <repo-url>
cd usim-management

composer install

cp .env.example .env
php artisan key:generate

# .env에서 DB_DATABASE, DB_USERNAME, DB_PASSWORD 설정 후
php artisan migrate --seed

php artisan serve
```

### 요구사항

- PHP 8.0 이상 (확장: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `zip`, `gd`)
- MySQL 8 또는 MariaDB 10.4+

### 테스트 계정 (시더로 자동 생성)

- 이메일: `admin@example.com`
- 비밀번호: `password1234`

## 엑셀 업로드 양식

1행 헤더 기준으로 upsert합니다 ("엑셀 다운로드"로 받은 파일을 그대로 편집해 재업로드하면 됩니다):

```
유심번호 | 일련번호 | 통신사 | 거래처/현장 | 고객명 | 기기모델 | 기기일련번호 | 상태 | 계약일 | 일시정지일 | 해지일 | 메모
```

- `유심번호`가 이미 있으면 갱신, 없으면 신규 등록
- `상태` 값이 바뀌면 이력이 자동 기록됨
- `고객명`, `기기일련번호`가 없는 값이면 새 레코드로 자동 생성

**[docs/sample_usim_data.xlsx](docs/sample_usim_data.xlsx)** — 위 양식 그대로 만든 예시 파일입니다
(전부 가상의 이름/번호로 채운 더미 데이터이며, 실제 고객 정보가 아닙니다). 업로드 화면에서 바로
테스트해볼 수 있습니다.
- `고객명`, `기기일련번호`가 없는 값이면 새 레코드로 자동 생성

## 폴더 구조 (핵심만)

```
app/
  Models/          Customer, Device, Usim, UsimStatusHistory, User
  Observers/       UsimObserver (상태 변경 이력 자동 기록)
  Console/Commands ImportLegacyUsimData (레거시 엑셀 → 이력 재구성 이관 스크립트)
  Http/Controllers Customer/Device/Usim/User/Dashboard 컨트롤러
  Http/Middleware  EnsureUserIsAdmin (관리자 전용 라우트 가드)
  Exports/Imports  엑셀 다운로드/업로드 (maatwebsite/excel)
database/
  migrations/      스키마 정의 (상태 이력 append-only 구조, 무중단 enum 변경 포함)
resources/views/   Blade + Bootstrap 5 뷰
```

## 라이선스

MIT
