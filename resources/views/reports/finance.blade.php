@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('subtitle', 'Pemasukan, pengeluaran, dan tunggakan')

@section('content')
<form class="row g-2 mb-3">
    <div class="col-md-3"><input class="form-control" type="month" name="period" value="{{ $period->format('Y-m') }}"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Tampilkan</button></div>
</form>
<div class="row g-3 mb-4">
    @foreach([
        ['Pemasukan', $income, 'success'],
        ['Pengeluaran', $expenses, 'danger'],
        ['Saldo Bersih', $net, 'primary'],
        ['Tunggakan', $arrears, 'warning'],
    ] as [$label, $value, $color])
        <div class="col-md-3">
            <div class="card stat-card"><div class="card-body">
                <div class="text-muted small">{{ $label }}</div>
                <div class="fs-4 fw-bold text-{{ $color }}">Rp {{ number_format($value, 0, ',', '.') }}</div>
            </div></div>
        </div>
    @endforeach
</div>
<div class="card mb-4">
    <div class="card-header bg-white fw-semibold">Pemasukan Terverifikasi</div>
    <table class="table mb-0">
        <thead><tr><th>Tanggal</th><th>Pelanggan</th><th>Kamar</th><th>Nominal</th></tr></thead>
        <tbody>
        @forelse($payments as $payment)
            <tr><td>{{ $payment->paid_at->format('d/m/Y') }}</td><td>{{ $payment->bill->customer->name }}</td><td>{{ $payment->bill->room->number }}</td><td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada pemasukan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="card mb-4">
    <div class="card-header bg-white fw-semibold">Pengeluaran</div>
    <table class="table mb-0">
        <thead><tr><th>Tanggal</th><th>Kategori</th><th>Deskripsi</th><th>Nominal</th></tr></thead>
        <tbody>
        @forelse($expenseRows as $expense)
            <tr><td>{{ $expense->spent_at->format('d/m/Y') }}</td><td>{{ $expense->category }}</td><td>{{ $expense->description }}</td><td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada pengeluaran.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="card">
    <div class="card-header bg-white fw-semibold">Tunggakan</div>
    <table class="table mb-0">
        <thead><tr><th>Periode</th><th>Pelanggan</th><th>Kamar</th><th>Status</th><th>Nominal</th></tr></thead>
        <tbody>
        @forelse($unpaidBills as $bill)
            <tr><td>{{ $bill->period->format('M Y') }}</td><td>{{ $bill->customer->name }}</td><td>{{ $bill->room->number }}</td><td>{{ $bill->status_label }}</td><td>Rp {{ number_format($bill->amount, 0, ',', '.') }}</td></tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada tunggakan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
