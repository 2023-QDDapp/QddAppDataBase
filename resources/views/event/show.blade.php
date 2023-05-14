@extends('layouts.private')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <strong>Datos de usuario</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto de usuario" class="rounded-circle" style="width: 150px; height: 150px;">
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nombre:</strong>
                            {{$user->nombre}}
                        </div>
                        <div class="col-md-6">
                            <strong>Edad:</strong>
                            {{$user->getAgeFromDate()}}
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Email:</strong>
                            {{$user->email}}
                        </div>
                        <div class="col-md-6">
                            <strong>Teléfono:</strong>
                            {{$user->telefono}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md">
                            <strong>Biografía:</strong>
                            {{$user->biografia}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <a class="btn btn-primary" href="{{ route('users.index') }}">Volver</a>
        </div>
    </div>
</div>
@endsection