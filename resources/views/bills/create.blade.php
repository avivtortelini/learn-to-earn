@extends('layouts.app')

@section('title', 'Tambah Tagihan')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('bills.store') }}" class="row g-3">
        @csrf
        <div class="col-md-6">
            <label class="form-label">Pelanggan</label>
            <select class="form-select" name="customer_id" required>
                <option value="">Pilih pelanggan</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                        {{ $customer->name }} - {{ $customer->activeOccupancy?->room?->number ?? 'tanpa kamar' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Periode</label>
            <input class="form-control" type="month" name="period" value="{{ old('period', now()->format('Y-m')) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Jatuh Tempo</label>
            <input class="form-control" type="date" name="due_date" value="{{ old('due_date', now()->endOfMonth()->format('Y-m-d')) }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea class="form-control" name="notes">{{ old('notes') }}</textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
@endsection
