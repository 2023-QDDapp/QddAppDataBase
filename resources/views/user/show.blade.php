@extends('layouts.private')

@section('content')
<div class="container">
    <strong>Datos de usuario</strong>
    <hr>
    <strong>Nombre: </strong>
    {{$user->nombre}}
    <br>
    <strong>Email: </strong>
    {{$user->email}}
    <br>
    
    <strong>Foto:</strong> <br>
        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto de usuario">

    <div class="row mt-4">
        <div class="col text-center">
            <a class="btn btn-primary" href="{{ route('users.index') }}">Volver</a>
        </div>
    </div>
</div>
@endsection