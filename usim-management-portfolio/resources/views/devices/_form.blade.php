<div class="mb-3">
    <label class="form-label">모델명 <span class="text-danger">*</span></label>
    <input type="text" name="model_name" class="form-control" value="{{ old('model_name', $device->model_name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">일련번호 (IMEI 등) <span class="text-danger">*</span></label>
    <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number', $device->serial_number ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">메모</label>
    <textarea name="memo" class="form-control" rows="3">{{ old('memo', $device->memo ?? '') }}</textarea>
</div>
