@extends('layouts.app')

@section('title', 'Kamar')
@section('subtitle', 'Data kamar dan status keterisian')
@section('actions')<a href="{{ route('rooms.create') }}" class="btn btn-primary">Tambah Kamar</a>@endsection

@section('content')
<form class="row g-2 mb-3">
    <div class="col-md-4"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Cari nomor kamar"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Cari</button></div>
</form>
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Nomor</th><th>Harga Bulanan</th><th>Status</th><th>Penghuni</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($rooms as $room)
                <tr>
                    <td class="fw-semibold">{{ $room->number }}</td>
                    <td>Rp {{ number_format($room->monthly_price, 0, ',', '.') }}</td>
                    <td><span class="badge text-bg-{{ $room->activeOccupancy ? 'success' : 'secondary' }}">{{ $room->status }}</span></td>
                    <td>{{ $room->activeOccupancy?->customer?->name ?? '-' }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('rooms.edit', $room) }}">Edit</a>
                        <form action="{{ route('rooms.destroy', $room) }}" method="post" class="d-inline">
                            @csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kamar ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kamar.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $rooms->links() }}</div>
@endsection
