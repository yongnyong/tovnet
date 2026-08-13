@extends('layouts.app')

@section('title', '유심 수정')

@section('content')
<h4 class="mb-3">유심 수정</h4>
<div class="card p-4" style="max-width: 700px;">
    <form method="POST" action="{{ route('usims.update', $usim) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">유심번호(ICCID) <span class="text-danger">*</span></label>
            <input type="text" name="usim_number" class="form-control" value="{{ old('usim_number', $usim->usim_number) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">일련번호</label>
            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $usim->phone_number) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">통신사</label>
            <input type="text" name="carrier" class="form-control" value="{{ old('carrier', $usim->carrier) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">거래처/현장</label>
            <input type="text" name="site" class="form-control" value="{{ old('site', $usim->site) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">고객</label>
            <select name="customer_id" class="form-select">
                <option value="">선택 안 함</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id', $usim->customer_id) == $customer->id)>{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">기기</label>
            <select name="device_id" class="form-select">
                <option value="">선택 안 함</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}" @selected(old('device_id', $usim->device_id) == $device->id)>{{ $device->model_name }} ({{ $device->serial_number }})</option>
                @endforeach
            </select>
        </div>

        <hr>
        <h6>상태 변경</h6>
        <div class="mb-3">
            <label class="form-label">상태 <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
                @foreach(\App\Models\Usim::STATUSES as $status)
                    <option value="{{ $status }}" @selected(old('status', $usim->status) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col mb-3">
                <label class="form-label">계약일</label>
                <input type="date" name="contract_date" class="form-control" value="{{ old('contract_date', optional($usim->contract_date)->toDateString()) }}">
            </div>
            <div class="col mb-3">
                <label class="form-label">일시정지일</label>
                <input type="date" name="suspended_date" class="form-control" value="{{ old('suspended_date', optional($usim->suspended_date)->toDateString()) }}">
            </div>
            <div class="col mb-3">
                <label class="form-label">해지일</label>
                <input type="date" name="canceled_date" class="form-control" value="{{ old('canceled_date', optional($usim->canceled_date)->toDateString()) }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">메모</label>
            <textarea name="memo" class="form-control" rows="3">{{ old('memo', $usim->memo) }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">저장</button>
        <a href="{{ route('usims.show', $usim) }}" class="btn btn-outline-secondary">취소</a>
    </form>
</div>
@endsection
