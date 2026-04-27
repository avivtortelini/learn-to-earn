@extends('layouts.app')

@section('title', 'Edit Kamar')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('rooms.update', $room) }}">
        @method('put')
        @include('rooms._form')
    </form>
</div></div>
@endsection
