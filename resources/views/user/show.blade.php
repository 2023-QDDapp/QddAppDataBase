@extends('layouts.private')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header custom-header-footer">
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
                            <strong>Categorías:</strong>
                            @forelse ($user->categorias as $categoria)
                                <span class="badge badge-primary">{{ $categoria->categoria }}</span>
                            @empty
                                <span>No tiene categorías asignadas.</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md">
                            <strong>Biografía:</strong>
                            {{$user->biografia}}
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md">
                            <strong>Eventos creados por el usuario:</strong>
                            @forelse ($user->eventosCreados as $evento)
                                <li style="list-style: none;">
                                    <span class="badge badge-primary">{{ $evento->titulo }}</span>
                                </li>
                            @empty
                                <br>
                                <span>No a creado eventos.</span>
                            @endforelse
                        </div>
                        <div class="col-md">
                            <strong>Eventos a los que asiste el usuario:</strong>
                            @forelse ($user->eventosAsistidos as $evento)
                                <li style="list-style: none;">
                                    <span class="badge badge-primary">{{ $evento->titulo }}</span>
                                </li>
                            @empty
                                <br>
                                <span>No asiste a eventos</span>
                            @endforelse
                        </div>
                    </div>
                    <hr>
                    
                </div>
            </div>
        </div>
        <div class="card-footer custom-header-footer">
            <a class="btn btn-primary" href="{{ route('users.index') }}">Volver</a>
        </div>
    </div>
</div>
@endsection