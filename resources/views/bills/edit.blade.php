@extends('layouts.app')

@section('title', 'Edit Tagihan')
@section('subtitle', $bill->customer->name.' · '.$bill->period->format('M Y'))
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('bills.update', $bill) }}" class="row g-3">
        @csrf @method('put')
        <div class="col-md-4">
            <label class="form-label">Nominal</label>
            <input class="form-control" type="number" name="amount" value="{{ old('amount', $bill->amount) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jatuh Tempo</label>
            <input class="form-control" type="date" name="due_date" value="{{ old('due_date', $bill->due_date->format('Y-m-d')) }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea class="form-control" name="notes">{{ old('notes', $bill->notes) }}</textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
@endsection
