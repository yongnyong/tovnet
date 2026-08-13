@extends('layouts.app')

@section('title', '유심 관리')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">유심 관리</h4>
    <div>
        <a href="{{ route('usims.export', request()->query()) }}" class="btn btn-outline-success">엑셀 다운로드</a>
        <a href="{{ route('usims.import.form') }}" class="btn btn-outline-primary">엑셀 업로드</a>
        <a href="{{ route('usims.create') }}" class="btn btn-primary">+ 유심 등록</a>
    </div>
</div>

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-auto">
            <input type="text" name="keyword" class="form-control" placeholder="유심번호/일련번호/고객명/기기번호 검색" value="{{ request('keyword') }}" style="min-width: 280px;">
        </div>
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">전체 상태</option>
                @foreach(\App\Models\Usim::STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="site" class="form-select">
                <option value="">전체 거래처</option>
                @foreach($sites as $site)
                    <option value="{{ $site }}" @selected(request('site') === $site)>{{ $site }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">검색</button>
            @if(request('keyword') || request('status') || request('site'))
                <a href="{{ route('usims.index') }}" class="btn btn-link">초기화</a>
            @endif
        </div>
    </form>
</div>

<div class="table-responsive p-2">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>유심번호</th>
                <th>일련번호</th>
                <th>거래처</th>
                <th>고객</th>
                <th>기기</th>
                <th>상태</th>
                <th>계약일</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($usims as $usim)
            <tr>
                <td>{{ $usim->id }}</td>
                <td><a href="{{ route('usims.show', $usim) }}">{{ $usim->usim_number }}</a></td>
                <td>{{ $usim->phone_number ?? '-' }}</td>
                <td>{{ $usim->site ?? '-' }}</td>
                <td>{{ $usim->customer->name ?? '-' }}</td>
                <td>{{ $usim->device->model_name ?? '-' }}</td>
                <td><span class="badge badge-status-{{ $usim->status }}">{{ $usim->status }}</span></td>
                <td>{{ optional($usim->contract_date)->format('Y-m-d') ?? '-' }}</td>
                <td class="text-end">
                    <a href="{{ route('usims.edit', $usim) }}" class="btn btn-sm btn-outline-secondary">수정</a>
                    <form action="{{ route('usims.destroy', $usim) }}" method="POST" class="d-inline" onsubmit="return confirm('삭제하시겠습니까?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">삭제</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="text-center text-muted py-4">등록된 유심이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $usims->links() }}
</div>
@endsection
