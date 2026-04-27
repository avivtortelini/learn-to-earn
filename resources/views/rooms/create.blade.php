@extends('layouts.app')

@section('title', 'Tambah Kamar')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('rooms.store') }}">
        @include('rooms._form')
    </form>
</div></div>
@endsection
