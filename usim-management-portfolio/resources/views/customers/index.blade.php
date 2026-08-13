@extends('layouts.app')

@section('title', '고객 관리')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">고객 관리</h4>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">+ 고객 등록</a>
</div>

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-auto">
            <input type="text" name="keyword" class="form-control" placeholder="이름/전화번호/이메일 검색" value="{{ request('keyword') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">검색</button>
            @if(request('keyword'))
                <a href="{{ route('customers.index') }}" class="btn btn-link">초기화</a>
            @endif
        </div>
    </form>
</div>

<div class="table-responsive p-2">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>이름</th>
                <th>전화번호</th>
                <th>이메일</th>
                <th>보유 유심 수</th>
                <th>등록일</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($customers as $customer)
            <tr>
                <td>{{ $customer->id }}</td>
                <td><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></td>
                <td>{{ $customer->phone ?? '-' }}</td>
                <td>{{ $customer->email ?? '-' }}</td>
                <td>{{ $customer->usims_count }}</td>
                <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                <td class="text-end">
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">수정</a>
                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('삭제하시겠습니까?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">삭제</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">등록된 고객이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $customers->links() }}
</div>
@endsection
