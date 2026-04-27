@extends('layouts.app')

@section('title', 'Upload Bukti Bayar')
@section('subtitle', $bill->customer->name.' · '.$bill->period->format('M Y'))
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('payments.store', $bill) }}" enctype="multipart/form-data" class="row g-3">
        @csrf
        <div class="col-md-4">
            <label class="form-label">Nominal</label>
            <input class="form-control" type="number" name="amount" value="{{ old('amount', $bill->amount) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tanggal Bayar</label>
            <input class="form-control" type="date" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Bukti Bayar</label>
            <input class="form-control" type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>
        <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea class="form-control" name="notes">{{ old('notes') }}</textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Upload</button>
            <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
@endsection
