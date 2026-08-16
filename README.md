# TOVNET | Field R&D & Development Portfolio

토브넷에서 수행한 **웹 시스템 개발, Vision AI 기술 분석, CCTV 네트워크 구축 및 현장 장비 관리 업무**를 프로젝트별로 정리한 포트폴리오 저장소입니다.

단순히 결과물만 정리하기보다, 실제 업무에서 맡은 역할과 문제 해결 과정, 사용 기술을 중심으로 기록했습니다.

---

## Projects

| Project                                                                               | Description                                     | Tech / Keywords                           |
| ------------------------------------------------------------------------------------- | ----------------------------------------------- | ----------------------------------------- |
| [MES 업무관리 플랫폼](./mes-portfolio/)                                                      | 제조·생산·재고·업무공유·고객 A/S를 통합 관리하는 사내 웹 시스템 개발       | PHP, MySQL, JavaScript, Bootstrap, Git    |
| [TOVNET-SEG](./tovnet-seg-portfolio/)                                                 | CCTV 영상 기반 수면 조류 세그멘테이션 AI 시스템 기술 인수 및 운영 구조 분석 | OneFormer, Swin, PyTorch, RTSP, Vision AI |
| [탄천 CCTV Network](./tancheon-cctv-network-portfolio/tancheon-cctv-network-portfolio/) | 탄천물재생센터 CCTV 원격 모니터링 환경 구축 및 네트워크 장애 분석         | RTSP, NAT, Port Forwarding, DHCP, Omada   |
| [USIM Management](./usim-management-portfolio/)                                       | 엑셀 기반 유심 관리 업무를 DB 기반 웹 시스템으로 전환                | Laravel, PHP, MySQL, Bootstrap, Excel     |

---

## 01. MES · 업무관리 플랫폼

### PHP 기반 제조·업무·고객 A/S 통합 관리 시스템

제품·부품·BOM·생산·재고·품질·시리얼 번호와 사내 업무공유, 주간보고, 고객 A/S를 하나의 웹 시스템에서 관리하는 프로젝트입니다.

주요 수행 내용:

* MAIN / SUB 구조의 업무관리 기능
* 업무 진행 상태 및 진척도 관리
* 주간보고와 기존 업무 데이터 연결
* 고객 A/S 접수 및 처리 프로세스
* HW / SW 프로젝트 칸반 관리
* Git 기반 협업 및 기능 통합

**Tech Stack**

`PHP` `MySQL / MariaDB` `JavaScript` `Bootstrap 5` `Fetch API` `Git`

➡️ [MES Portfolio 바로가기](./mes-portfolio/)

---

## 02. TOVNET-SEG

### CCTV 기반 수면 조류 Segmentation AI System

정부과제로 개발된 CCTV 기반 수면 조류 분석 시스템의 기술자료와 코드를 인수하여 전체 AI 파이프라인을 분석하고, 운영 및 유지보수 관점에서 시스템 구조를 정리한 프로젝트입니다.

주요 수행 내용:

* RTSP CCTV 영상 입력 구조 분석
* Semantic Segmentation 추론 파이프라인 분석
* OneFormer + Swin 기반 모델 구조 검토
* 영상 입력 → 추론 → 후처리 → DB → 외부 시스템 전송 흐름 분석
* 메모리 증가 및 RTSP 장시간 운영 이슈 검토
* 정부과제 기술 발표자료 작성 및 발표
* 현장 적용 및 고도화 방향 검토

**Tech Stack**

`Python` `PyTorch` `OneFormer` `Swin Transformer` `RTSP` `FFmpeg` `REST API` `MQTT`

➡️ [TOVNET-SEG Portfolio 바로가기](./tovnet-seg-portfolio/)

---

## 03. 탄천 CCTV Network

### CCTV 원격 모니터링 네트워크 구축 및 Troubleshooting

탄천물재생센터 현장에서 CCTV, PTZ 및 열화상 카메라의 원격 영상 접속 환경을 점검하고 네트워크 장애 원인을 분석한 프로젝트입니다.

주요 수행 내용:

* 현장 CCTV 및 네트워크 장비 연결
* 카메라 내부 IP 및 MAC 확인
* RTSP / WEB 영상 접속 테스트
* NAT 및 Port Forwarding 구성 점검
* DHCP 환경에서 발생하는 IP 변경 문제 분석
* DHCP Reservation / Fixed IP 운영 방식 검토
* `ping`, `arp`, `Test-NetConnection`을 이용한 단계별 네트워크 진단
* Omada / ER605 기반 네트워크 환경 점검

**Keywords**

`RTSP` `TCP/IP` `NAT` `Port Forwarding` `DHCP` `Omada` `ER605` `CCTV`

➡️ [CCTV Network Portfolio 바로가기](./tancheon-cctv-network-portfolio/tancheon-cctv-network-portfolio/)

---

## 04. USIM Management System

### 엑셀 기반 관리 업무의 웹 시스템 전환

현장에서 엑셀로 관리하던 USIM 계약 및 상태 정보를 데이터베이스 기반 웹 시스템으로 전환한 프로젝트입니다.

주요 수행 내용:

* 고객 / USIM / 기기 정보 관리
* 사용중 / 일시정지 / 해지 상태 관리
* 상태 변경 이력 자동 기록
* 관리자 / 직원 권한 분리
* 엑셀 데이터 업로드 및 다운로드
* 기존 운영 엑셀 데이터 DB 이관
* 상태별 현황 대시보드

**Tech Stack**

`Laravel` `PHP` `MySQL / MariaDB` `Blade` `Bootstrap 5` `PhpSpreadsheet`

➡️ [USIM Management Portfolio 바로가기](./usim-management-portfolio/)

---

## Experience Overview

이 저장소의 프로젝트들은 크게 세 가지 영역의 실무 경험을 보여줍니다.

### Web / Backend

* PHP · Laravel 기반 업무 시스템 개발
* MySQL / MariaDB 데이터 관리
* REST 형태의 데이터 처리
* 기존 엑셀 업무의 웹·DB 시스템 전환
* Git 기반 협업 및 변경사항 관리

### Vision AI

* CCTV 영상 기반 Semantic Segmentation
* AI 모델 및 학습·추론 파이프라인 분석
* RTSP 실시간 영상 처리 구조 이해
* 운영 환경에서 발생하는 메모리·프로세스 문제 분석
* 연구개발 결과물 기술 인수 및 문서화

### Network / Field Engineering

* CCTV 및 NVR 네트워크 구성
* RTSP 영상 스트림 확인
* NAT / Port Forwarding
* DHCP / Fixed IP
* 공유기·게이트웨이·PoE 장비 점검
* 현장 네트워크 Troubleshooting

---

## Repository Structure

```text
tovnet/
│
├── mes-portfolio/
│   └── MES · 업무관리 플랫폼
│
├── tovnet-seg-portfolio/
│   └── CCTV 기반 수면 조류 Segmentation
│
├── tancheon-cctv-network-portfolio/
│   └── 탄천 CCTV 네트워크 구축 및 Troubleshooting
│
├── usim-management-portfolio/
│   └── USIM 관리 시스템
│
└── README.md
```

---

## About This Repository

본 저장소는 토브넷 업무 과정에서 수행한 프로젝트 중 **공개 가능한 범위의 개발 과정, 기술 분석 및 포트폴리오 자료**를 정리하기 위해 구성했습니다.

실제 서버 접속 정보, 계정·비밀번호, 고객 개인정보 및 기타 민감한 운영 정보는 공개하지 않습니다.
