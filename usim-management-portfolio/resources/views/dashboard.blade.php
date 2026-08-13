@extends('layouts.app')

@section('title', '대시보드')

@section('content')
<h4 class="mb-3">대시보드</h4>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center">
            <div class="text-muted small">전체 유심</div>
            <div class="fs-3 fw-bold">{{ $stats['total_usims'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center">
            <div class="text-muted small">전체 고객</div>
            <div class="fs-3 fw-bold">{{ $stats['total_customers'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center">
            <div class="text-muted small">전체 기기</div>
            <div class="fs-3 fw-bold">{{ $stats['total_devices'] }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('usims.index', ['status' => '사용중']) }}" class="text-decoration-none">
            <div class="card p-3 text-center border-start border-success border-4">
                <div class="text-muted small">사용중</div>
                <div class="fs-3 fw-bold text-success">{{ $stats['contract'] }}</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('usims.index', ['status' => '일시정지']) }}" class="text-decoration-none">
            <div class="card p-3 text-center border-start border-warning border-4">
                <div class="text-muted small">일시정지</div>
                <div class="fs-3 fw-bold text-warning">{{ $stats['suspended'] }}</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('usims.index', ['status' => '해지']) }}" class="text-decoration-none">
            <div class="card p-3 text-center border-start border-secondary border-4">
                <div class="text-muted small">해지</div>
                <div class="fs-3 fw-bold text-secondary">{{ $stats['canceled'] }}</div>
            </div>
        </a>
    </div>
</div>

<div class="card p-3">
    <h6 class="mb-3">최근 상태 변경 이력</h6>
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead>
                <tr>
                    <th>유심번호</th>
                    <th>상태</th>
                    <th>메모</th>
                    <th>처리자</th>
                    <th>일자</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recentHistories as $history)
                <tr>
                    <td>
                        @if($history->usim)
                            <a href="{{ route('usims.show', $history->usim) }}">{{ $history->usim->usim_number }}</a>
                        @else - @endif
                    </td>
                    <td><span class="badge badge-status-{{ $history->status }}">{{ $history->status }}</span></td>
                    <td>{{ $history->memo }}</td>
                    <td>{{ $history->changedByUser->name ?? '-' }}</td>
                    <td>{{ $history->changed_date->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-3">이력이 없습니다.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
