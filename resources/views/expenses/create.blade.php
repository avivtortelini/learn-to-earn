@extends('layouts.app')

@section('title', 'Tambah Pengeluaran')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('expenses.store') }}">
        @include('expenses._form')
    </form>
</div></div>
@endsection
