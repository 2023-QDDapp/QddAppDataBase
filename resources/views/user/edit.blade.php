@extends('layouts.private')

@section('title', "Qdd - Editar usuario")

@section('content')
<div class="container">
    <form action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('user._fields', ['modo' => 'Editar'])

    </form>
</div>
@endsection