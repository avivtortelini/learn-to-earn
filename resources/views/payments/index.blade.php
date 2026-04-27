@extends('layouts.app')

@section('title', 'Pembayaran')
@section('subtitle', 'Bukti bayar dan verifikasi')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Tanggal</th><th>Pelanggan</th><th>Kamar</th><th>Nominal</th><th>Bukti</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->paid_at->format('d/m/Y') }}</td>
                    <td>{{ $payment->bill->customer->name }}</td>
                    <td>{{ $payment->bill->room->number }}</td>
                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td><a href="{{ asset('storage/'.$payment->proof_path) }}" target="_blank">Lihat</a></td>
                    <td><span class="badge text-bg-{{ $payment->status === 'verified' ? 'success' : 'warning' }}">{{ $payment->status }}</span></td>
                    <td class="text-end">
                        @if($payment->status === 'pending')
                            <form action="{{ route('payments.verify', $payment) }}" method="post" class="d-inline">
                                @csrf @method('patch')
                                <button class="btn btn-sm btn-success">Verifikasi</button>
                            </form>
                        @endif
                        <form action="{{ route('payments.destroy', $payment) }}" method="post" class="d-inline">
                            @csrf @method('delete')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pembayaran ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $payments->links() }}</div>
@endsection
