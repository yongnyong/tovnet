@extends('layouts.app')

@section('title', '유심 등록')

@section('content')
<h4 class="mb-3">유심 등록</h4>
<div class="card p-4" style="max-width: 700px;">
    <form method="POST" action="{{ route('usims.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">유심번호(ICCID) <span class="text-danger">*</span></label>
            <input type="text" name="usim_number" class="form-control" value="{{ old('usim_number') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">일련번호</label>
            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">통신사</label>
            <input type="text" name="carrier" class="form-control" value="{{ old('carrier') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">거래처/현장</label>
            <input type="text" name="site" class="form-control" value="{{ old('site') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">고객</label>
            <select name="customer_id" class="form-select">
                <option value="">선택 안 함</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">기기</label>
            <select name="device_id" class="form-select">
                <option value="">선택 안 함</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}" @selected(old('device_id') == $device->id)>{{ $device->model_name }} ({{ $device->serial_number }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">계약일 <span class="text-danger">*</span></label>
            <input type="date" name="contract_date" class="form-control" value="{{ old('contract_date', now()->toDateString()) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">메모</label>
            <textarea name="memo" class="form-control" rows="3">{{ old('memo') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">등록</button>
        <a href="{{ route('usims.index') }}" class="btn btn-outline-secondary">취소</a>
    </form>
</div>
@endsection
