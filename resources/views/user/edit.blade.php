@extends('layouts.private')

@section('content')
<div class="container">
    <strong>Actualizar datos de administrador</strong>
    <form action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('user._fields', ['modo' => 'Editar'])

    </form>
</div>
@endsection