@extends('layouts.app')

@section('title', 'Edit Pelanggan')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('customers.update', $customer) }}">
        @method('put')
        @include('customers._form')
    </form>
</div></div>
@endsection
