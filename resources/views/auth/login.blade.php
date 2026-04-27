<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · Manajemen Kost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="min-vh-100 d-flex align-items-center justify-content-center p-4">
    <form class="card shadow-sm border-0 p-4" style="width: 420px" method="post" action="{{ route('login.store') }}">
        @csrf
        <h1 class="h4 mb-1">Manajemen Kost</h1>
        <p class="text-muted mb-4">Masuk sebagai receptionist atau pemilik.</p>
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Ingat saya</label>
        </div>
        <button class="btn btn-primary w-100">Login</button>
        <div class="small text-muted mt-3">Seeder: pemilik@kost.local / receptionist@kost.local, password: password</div>
    </form>
</main>
</body>
</html>
