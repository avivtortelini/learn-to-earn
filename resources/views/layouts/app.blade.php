<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Manajemen Kost')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .sidebar { width: 260px; min-height: 100vh; background: #172033; }
        .sidebar a { color: #dbe4f0; text-decoration: none; border-radius: .5rem; }
        .sidebar a:hover, .sidebar a.active { background: #25324a; color: #fff; }
        .content { min-width: 0; }
        .stat-card { border: 0; box-shadow: 0 8px 24px rgba(23, 32, 51, .08); }
    </style>
</head>
<body>
<div class="d-flex">
    <aside class="sidebar p-3 d-flex flex-column">
        <div class="text-white fw-bold fs-5 mb-4">Manajemen Kost</div>
        @php($current = request()->route()?->getName())
        <nav class="d-grid gap-1">
            <a class="px-3 py-2 {{ $current === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="px-3 py-2 {{ str_starts_with($current, 'rooms.') ? 'active' : '' }}" href="{{ route('rooms.index') }}">Kamar</a>
            <a class="px-3 py-2 {{ str_starts_with($current, 'customers.') ? 'active' : '' }}" href="{{ route('customers.index') }}">Pelanggan</a>
            <a class="px-3 py-2 {{ str_starts_with($current, 'bills.') ? 'active' : '' }}" href="{{ route('bills.index') }}">Tagihan</a>
            <a class="px-3 py-2 {{ str_starts_with($current, 'payments.') ? 'active' : '' }}" href="{{ route('payments.index') }}">Pembayaran</a>
            <a class="px-3 py-2 {{ str_starts_with($current, 'expenses.') ? 'active' : '' }}" href="{{ route('expenses.index') }}">Pengeluaran</a>
            @if(auth()->user()->isOwner())
                <a class="px-3 py-2 {{ str_starts_with($current, 'reports.') ? 'active' : '' }}" href="{{ route('reports.finance') }}">Laporan</a>
                <a class="px-3 py-2 {{ str_starts_with($current, 'users.') ? 'active' : '' }}" href="{{ route('users.index') }}">User</a>
            @endif
        </nav>
        <form action="{{ route('logout') }}" method="post" class="mt-auto">
            @csrf
            <div class="text-white-50 small mb-2">{{ auth()->user()->name }} · {{ auth()->user()->role }}</div>
            <button class="btn btn-outline-light w-100">Logout</button>
        </form>
    </aside>

    <main class="content flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">@yield('title')</h1>
                <div class="text-muted">@yield('subtitle')</div>
            </div>
            @yield('actions')
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Periksa input:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
