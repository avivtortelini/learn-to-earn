@extends('layouts.app')

@section('title', 'Edit User')
@section('content')
<div class="card"><div class="card-body">
    <form method="post" action="{{ route('users.update', $user) }}">
        @method('put')
        @include('users._form')
    </form>
</div></div>
@endsection
