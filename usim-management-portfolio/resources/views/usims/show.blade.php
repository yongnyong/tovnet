@extends('layouts.app')

@section('title', $usim->usim_number . ' - 유심 상세')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
        {{ $usim->usim_number }}
        <span class="badge badge-status-{{ $usim->status }}">{{ $usim->status }}</span>
    </h4>
    <div>
        <a href="{{ route('usims.edit', $usim) }}" class="btn btn-outline-secondary">수정</a>
        <a href="{{ route('usims.index') }}" class="btn btn-outline-secondary">목록</a>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card p-3 mb-3">
            <dl class="row mb-0">
                <dt class="col-4">일련번호</dt><dd class="col-8">{{ $usim->phone_number ?? '-' }}</dd>
                <dt class="col-4">통신사</dt><dd class="col-8">{{ $usim->carrier ?? '-' }}</dd>
                <dt class="col-4">거래처/현장</dt><dd class="col-8">{{ $usim->site ?? '-' }}</dd>
                <dt class="col-4">고객</dt>
                <dd class="col-8">
                    @if($usim->customer)
                        <a href="{{ route('customers.show', $usim->customer) }}">{{ $usim->customer->name }}</a>
                    @else - @endif
                </dd>
                <dt class="col-4">기기</dt><dd class="col-8">{{ $usim->device->model_name ?? '-' }} @if($usim->device)({{ $usim->device->serial_number }})@endif</dd>
                <dt class="col-4">계약일</dt><dd class="col-8">{{ optional($usim->contract_date)->format('Y-m-d') ?? '-' }}</dd>
                <dt class="col-4">일시정지일</dt><dd class="col-8">{{ optional($usim->suspended_date)->format('Y-m-d') ?? '-' }}</dd>
                <dt class="col-4">해지일</dt><dd class="col-8">{{ optional($usim->canceled_date)->format('Y-m-d') ?? '-' }}</dd>
                <dt class="col-4">메모</dt><dd class="col-8">{{ $usim->memo ?? '-' }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card p-3">
            <h6 class="mb-3">상태 변경 이력</h6>
            <ul class="list-group list-group-flush">
                @forelse($usim->statusHistories as $history)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge badge-status-{{ $history->status }}">{{ $history->status }}</span>
                            <span class="ms-2">{{ $history->memo }}</span>
                            <div class="text-muted small">
                                처리자: {{ $history->changedByUser->name ?? '-' }}
                            </div>
                        </div>
                        <span class="text-muted small">{{ $history->changed_date->format('Y-m-d') }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted text-center">이력이 없습니다.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
