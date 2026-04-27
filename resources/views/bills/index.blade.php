@extends('layouts.app')

@section('title', 'Tagihan')
@section('subtitle', 'Tagihan bulanan penghuni')
@section('actions')
<div class="d-flex gap-2">
    <a href="{{ route('bills.generator') }}" class="btn btn-outline-primary">Generate Bulanan</a>
    <a href="{{ route('bills.create') }}" class="btn btn-primary">Tambah Tagihan</a>
</div>
@endsection

@section('content')
<form class="row g-2 mb-3">
    <div class="col-md-3"><input class="form-control" type="month" name="period" value="{{ request('period') }}"></div>
    <div class="col-md-3">
        <select class="form-select" name="status">
            <option value="">Semua status</option>
            @foreach(['unpaid' => 'Belum Bayar', 'pending' => 'Menunggu Verifikasi', 'paid' => 'Lunas'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button></div>
</form>
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Periode</th><th>Pelanggan</th><th>Kamar</th><th>Nominal</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($bills as $bill)
                <tr>
                    <td>{{ $bill->period->format('M Y') }}</td>
                    <td>{{ $bill->customer->name }}</td>
                    <td>{{ $bill->room->number }}</td>
                    <td>Rp {{ number_format($bill->amount, 0, ',', '.') }}</td>
                    <td><span class="badge text-bg-{{ $bill->status === 'paid' ? 'success' : ($bill->status === 'pending' ? 'warning' : 'danger') }}">{{ $bill->status_label }}</span></td>
                    <td class="text-end">
                        @if($bill->status !== 'paid')<a class="btn btn-sm btn-outline-success" href="{{ route('payments.create', $bill) }}">Upload Bukti</a>@endif
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('bills.edit', $bill) }}">Edit</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada tagihan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $bills->links() }}</div>
@endsection
