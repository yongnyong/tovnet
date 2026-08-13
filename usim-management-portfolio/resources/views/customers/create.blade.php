@extends('layouts.app')

@section('title', '고객 등록')

@section('content')
<h4 class="mb-3">고객 등록</h4>
<div class="card p-4" style="max-width: 600px;">
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf
        @include('customers._form')
        <button type="submit" class="btn btn-primary">등록</button>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">취소</a>
    </form>
</div>
@endsection
