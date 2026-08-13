@extends('layouts.app')

@section('title', '사용자 관리')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">사용자 관리</h4>
    <a href="{{ route('users.create') }}" class="btn btn-primary">+ 사용자 등록</a>
</div>

<div class="table-responsive p-2">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>이름</th>
                <th>이메일</th>
                <th>권한</th>
                <th>상태</th>
                <th>가입일</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->isAdmin() ? '관리자' : '직원' }}</td>
                <td>
                    @if($user->is_active)
                        <span class="badge bg-success">활성</span>
                    @else
                        <span class="badge bg-secondary">비활성</span>
                    @endif
                </td>
                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                <td class="text-end">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">수정</a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('삭제하시겠습니까?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">삭제</button>
                    </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $users->links() }}
</div>
@endsection
