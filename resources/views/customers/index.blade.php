@extends('layouts.app')

@section('title', 'Pelanggan')
@section('subtitle', 'Data penghuni dan kamar aktif')
@section('actions')<a href="{{ route('customers.create') }}" class="btn btn-primary">Tambah Pelanggan</a>@endsection

@section('content')
<form class="row g-2 mb-3">
    <div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nama atau telepon"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Cari</button></div>
</form>
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Nama</th><th>Telepon</th><th>Kamar</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td class="fw-semibold">{{ $customer->name }}</td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->activeOccupancy?->room?->number ?? '-' }}</td>
                    <td><span class="badge text-bg-{{ $customer->status === 'active' ? 'success' : 'secondary' }}">{{ $customer->status }}</span></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('customers.edit', $customer) }}">Edit</a>
                        @if($customer->status === 'active')
                            <form action="{{ route('customers.deactivate', $customer) }}" method="post" class="d-inline">
                                @csrf @method('patch')
                                <button class="btn btn-sm btn-outline-warning" onclick="return confirm('Nonaktifkan pelanggan ini?')">Nonaktifkan</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pelanggan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>
@endsection
