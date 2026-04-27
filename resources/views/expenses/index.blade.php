@extends('layouts.app')

@section('title', 'Pengeluaran')
@section('subtitle', 'Biaya operasional kost')
@section('actions')<a href="{{ route('expenses.create') }}" class="btn btn-primary">Tambah Pengeluaran</a>@endsection

@section('content')
<form class="row g-2 mb-3">
    <div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari kategori atau deskripsi"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Cari</button></div>
</form>
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Nominal</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->spent_at->format('d/m/Y') }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->description ?? '-' }}</td>
                    <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('expenses.edit', $expense) }}">Edit</a>
                        <form action="{{ route('expenses.destroy', $expense) }}" method="post" class="d-inline">
                            @csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pengeluaran ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengeluaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $expenses->links() }}</div>
@endsection
