@extends('layouts.app')

@section('title', 'Edit Pengeluaran')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('expenses.update', $expense) }}">
        @method('put')
        @include('expenses._form')
    </form>
</div></div>
@endsection
