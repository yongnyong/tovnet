<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '유심관리시스템')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f6fa; }
        .navbar-brand { font-weight: 700; }
        .badge-status-사용중 { background-color: #198754; }
        .badge-status-일시정지 { background-color: #fd7e14; }
        .badge-status-해지 { background-color: #6c757d; }
        .table-responsive { background: #fff; border-radius: .5rem; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">유심관리시스템</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">대시보드</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('usims.*') ? 'active' : '' }}" href="{{ route('usims.index') }}">유심 관리</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">고객 관리</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('devices.*') ? 'active' : '' }}" href="{{ route('devices.index') }}">기기 관리</a>
                    </li>
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">사용자 관리</a>
                    </li>
                    @endif
                </ul>
                <span class="navbar-text text-light me-3">
                    {{ auth()->user()->name }}님 ({{ auth()->user()->isAdmin() ? '관리자' : '직원' }})
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm" type="submit">로그아웃</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
