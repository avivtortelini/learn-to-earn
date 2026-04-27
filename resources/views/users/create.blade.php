@extends('layouts.app')

@section('title', 'Tambah User')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('users.store') }}">
        @include('users._form')
    </form>
</div></div>
@endsection
