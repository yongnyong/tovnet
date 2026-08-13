<div class="mb-3">
    <label class="form-label">이름 <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">전화번호</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">이메일</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">주소</label>
    <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">메모</label>
    <textarea name="memo" class="form-control" rows="3">{{ old('memo', $customer->memo ?? '') }}</textarea>
</div>
