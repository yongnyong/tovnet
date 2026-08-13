@extends('layouts.app')

@section('title', '사용자 등록')

@section('content')
<h4 class="mb-3">사용자 등록</h4>
<div class="card p-4" style="max-width: 500px;">
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">이름 <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">이메일 <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">비밀번호 <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">권한 <span class="text-danger">*</span></label>
            <select name="role" class="form-select" required>
                <option value="staff" @selected(old('role') === 'staff')>직원</option>
                <option value="admin" @selected(old('role') === 'admin')>관리자</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">등록</button>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">취소</a>
    </form>
</div>
@endsection
