@extends('layouts.app')

@section('title', '고객 수정')

@section('content')
<h4 class="mb-3">고객 수정</h4>
<div class="card p-4" style="max-width: 600px;">
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')
        @include('customers._form')
        <button type="submit" class="btn btn-primary">저장</button>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">취소</a>
    </form>
</div>
@endsection
