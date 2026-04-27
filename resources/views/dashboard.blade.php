@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan operasional bulan ini')

@section('content')
<div class="row g-3 mb-4">
    @foreach([
        ['Total Kamar', $totalRooms],
        ['Kamar Terisi', $occupiedRooms],
        ['Kamar Kosong', $emptyRooms],
        ['Tagihan Belum Lunas', $unpaidBills],
    ] as [$label, $value])
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-3 fw-bold">{{ $value }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Pemasukan Bulan Ini</div>
                <div class="fs-3 fw-bold">Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Pengeluaran Bulan Ini</div>
                <div class="fs-3 fw-bold">Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>
<div class="card mt-4">
    <div class="card-header bg-white fw-semibold">Pembayaran Terbaru</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Pelanggan</th><th>Kamar</th><th>Nominal</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
            @forelse($recentPayments as $payment)
                <tr>
                    <td>{{ $payment->bill->customer->name }}</td>
                    <td>{{ $payment->bill->room->number }}</td>
                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td><span class="badge text-bg-{{ $payment->status === 'verified' ? 'success' : 'warning' }}">{{ $payment->status }}</span></td>
                    <td>{{ $payment->paid_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
