@extends('layouts.app')

@section('title', 'Generate Tagihan Bulanan')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('bills.generate') }}" class="row g-3">
        @csrf
        <div class="col-md-4">
            <label class="form-label">Periode</label>
            <input class="form-control" type="month" name="period" value="{{ old('period', now()->format('Y-m')) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jatuh Tempo</label>
            <input class="form-control" type="date" name="due_date" value="{{ old('due_date', now()->endOfMonth()->format('Y-m-d')) }}" required>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Generate</button>
            <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
@endsection
