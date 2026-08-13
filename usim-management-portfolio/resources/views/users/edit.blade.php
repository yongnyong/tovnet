@extends('layouts.app')

@section('title', '사용자 수정')

@section('content')
<h4 class="mb-3">사용자 수정</h4>
<div class="card p-4" style="max-width: 500px;">
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">이름 <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">이메일 <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">비밀번호 (변경 시에만 입력)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">권한 <span class="text-danger">*</span></label>
            <select name="role" class="form-select" required>
                <option value="staff" @selected(old('role', $user->role) === 'staff')>직원</option>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>관리자</option>
            </select>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $user->is_active))>
            <label class="form-check-label" for="is_active">계정 활성화</label>
        </div>
        <button type="submit" class="btn btn-primary">저장</button>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">취소</a>
    </form>
</div>
@endsection
