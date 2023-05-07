@extends('layouts.private')

@section('content')
<div class="container">
    <strong>Datos de usuario</strong>
    <hr>
    <strong>Nombre: </strong>
    {{$user->nombre}}
    <br>
    <strong>email: </strong>
    {{$user->email}}
    <br>
    
    <div class="row mt-4">
        <div class="col text-center">
            <a class="btn btn-primary" href="{{ route('users.index') }}">Volver</a>
        </div>
    </div>

</div>
@endsection