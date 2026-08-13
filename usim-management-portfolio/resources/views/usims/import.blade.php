@extends('layouts.app')

@section('title', '유심 엑셀 업로드')

@section('content')
<h4 class="mb-3">유심 엑셀 업로드</h4>
<div class="card p-4" style="max-width: 600px;">
    <p class="text-muted">
        "엑셀 다운로드"로 받은 양식과 동일한 헤더(유심번호, 일련번호, 통신사, 거래처/현장, 고객명, 기기모델, 기기일련번호, 상태, 계약일, 일시정지일, 해지일, 메모)를 사용해주세요.<br>
        유심번호가 이미 존재하면 정보가 업데이트되고, 없으면 새로 등록됩니다.
    </p>
    <form method="POST" action="{{ route('usims.import') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">엑셀 파일 (.xlsx, .xls, .csv)</label>
            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
        </div>
        <button type="submit" class="btn btn-primary">업로드</button>
        <a href="{{ route('usims.index') }}" class="btn btn-outline-secondary">취소</a>
    </form>
</div>
@endsection
