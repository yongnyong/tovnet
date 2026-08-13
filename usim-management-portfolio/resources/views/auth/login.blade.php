<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>로그인 - 유심관리시스템</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f6fa; height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 380px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card p-4">
            <h4 class="text-center mb-4">유심관리시스템</h4>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">이메일</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">비밀번호</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">로그인 상태 유지</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">로그인</button>
            </form>
        </div>
    </div>
</body>
</html>
