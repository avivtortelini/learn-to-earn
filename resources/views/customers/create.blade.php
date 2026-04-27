@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('customers.store') }}">
        @include('customers._form')
    </form>
</div></div>
@endsection
