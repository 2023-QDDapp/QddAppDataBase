@extends('layouts.private')

@section('content')
<div class="container">
    <strong>Datos de administrador</strong>
    <hr>
    <strong>Nombre: </strong>
    {{$admin->name}}
    <br>
    <strong>email: </strong>
    {{$admin->email}}
    <br>
    
    <div class="row mt-4">
        <div class="col text-center">
            <a class="btn btn-primary" href="{{ route('admins.index') }}">Volver</a>
        </div>
    </div>

</div>
@endsection