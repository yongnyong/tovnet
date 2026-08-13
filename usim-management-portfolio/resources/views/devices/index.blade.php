@extends('layouts.app')

@section('title', '기기 관리')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">기기 관리</h4>
    <a href="{{ route('devices.create') }}" class="btn btn-primary">+ 기기 등록</a>
</div>

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-auto">
            <input type="text" name="keyword" class="form-control" placeholder="모델명/일련번호 검색" value="{{ request('keyword') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">검색</button>
            @if(request('keyword'))
                <a href="{{ route('devices.index') }}" class="btn btn-link">초기화</a>
            @endif
        </div>
    </form>
</div>

<div class="table-responsive p-2">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>모델명</th>
                <th>일련번호</th>
                <th>연결된 유심</th>
                <th>등록일</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($devices as $device)
            <tr>
                <td>{{ $device->id }}</td>
                <td>{{ $device->model_name }}</td>
                <td>{{ $device->serial_number }}</td>
                <td>
                    @if($device->usim)
                        <a href="{{ route('usims.show', $device->usim) }}">{{ $device->usim->usim_number }}</a>
                    @else
                        <span class="text-muted">미배정</span>
                    @endif
                </td>
                <td>{{ $device->created_at->format('Y-m-d') }}</td>
                <td class="text-end">
                    <a href="{{ route('devices.edit', $device) }}" class="btn btn-sm btn-outline-secondary">수정</a>
                    <form action="{{ route('devices.destroy', $device) }}" method="POST" class="d-inline" onsubmit="return confirm('삭제하시겠습니까?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">삭제</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">등록된 기기가 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $devices->links() }}
</div>
@endsection
