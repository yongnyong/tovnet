@extends('layouts.app')

@section('title', $customer->name . ' - 고객 상세')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $customer->name }}</h4>
    <div>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary">수정</a>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">목록</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card p-3 mb-3">
            <dl class="row mb-0">
                <dt class="col-4">전화번호</dt><dd class="col-8">{{ $customer->phone ?? '-' }}</dd>
                <dt class="col-4">이메일</dt><dd class="col-8">{{ $customer->email ?? '-' }}</dd>
                <dt class="col-4">주소</dt><dd class="col-8">{{ $customer->address ?? '-' }}</dd>
                <dt class="col-4">메모</dt><dd class="col-8">{{ $customer->memo ?? '-' }}</dd>
                <dt class="col-4">등록일</dt><dd class="col-8">{{ $customer->created_at->format('Y-m-d') }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card p-3">
            <h6 class="mb-3">보유 유심 ({{ $customer->usims->count() }}건)</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>유심번호</th>
                            <th>전화번호</th>
                            <th>기기</th>
                            <th>상태</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($customer->usims as $usim)
                        <tr>
                            <td><a href="{{ route('usims.show', $usim) }}">{{ $usim->usim_number }}</a></td>
                            <td>{{ $usim->phone_number ?? '-' }}</td>
                            <td>{{ $usim->device->model_name ?? '-' }}</td>
                            <td><span class="badge badge-status-{{ $usim->status }}">{{ $usim->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">보유 유심이 없습니다.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
