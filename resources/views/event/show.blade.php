@extends('layouts.private')

@section('content')

<div class="container">
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header custom-header-footer">
            <h4><strong>Datos del evento</strong></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $event->imagen) }}" alt="Foto de usuario" class="rounded" style="width: 300px;">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Título:</strong>
                            {{$event->titulo}}
                        </div>
                        <div class="col-md-6">
                            <strong>Categoría:</strong>
                            <span class="badge badge-primary">{{$event->categoria->categoria}}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                       <div class="col-md-6">
                            <strong>Organizador:</strong>
                            {{$event->creador->nombre}}
                            <img src="{{ asset('storage/' . $event->creador->foto) }}" alt="{{ $event->creador->nombre }}" class="rounded-circle" style="width: 50px; height: 50px;">
                        </div>
                        <div class="col-md-6">
                            <strong>Número de participantes:</strong>
                            {{$event->n_participantes}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md">
                            <strong>Fecha y hora de inicio:</strong>
                            {{$event->fecha_hora_inicio}}
                        </div>
                        <div class="col-md">
                            <strong>Fecha y hora de fin:</strong>
                            {{$event->fecha_hora_fin}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md">
                            <strong>Descripción:</strong>
                            {{$event->descripcion}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md">
                            <strong>Localización:</strong>
                            {{$event->location}}
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>Asistentes:</strong>
                    <hr>
                    <div style="max-height: 300px; overflow-y: auto;"> 
                        @forelse ($event->usuariosAsistentes as $user)
                            @if ($user->pivot->estado == true)
                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nombre }}" class="rounded-circle" style="width: 50px; height: 50px;">
                                    <p class="ml-3 mb-0"><strong>{{ $user->nombre }}</strong></p>
                                    <div class="ml-auto">
                                        <form action="{{ route('eventousers.destroy', $user->pivot->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Está seguro de eliminar al usuario del evento?')" class="btn btn-link text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <hr>
                            @endif
                        @empty
                            <span>No hay ningún asistente.</span>
                        @endforelse
                    </div>
                </div>
                <div class="col-md-6" >
                    <strong>Pendientes:</strong>
                    <hr>
                    <div style="max-height: 300px; overflow-y: auto;">
                        @forelse ($event->usuariosAsistentes as $user)
                            @if ($user->pivot->estado == false)
                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ asset('storage/' . $user->foto) }}" alt="{{ $user->nombre }}" class="rounded-circle" style="width: 50px; height: 50px;">
                                    <p class="ml-3 mb-0"><strong>{{ $user->nombre }}</strong></p>
                                    <div class="ml-auto">
                                        <form action="{{ route('eventousers.update', $user->pivot->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-link text-success" onclick="return confirm('¿Está seguro de que desea aceptar al usuario en el evento?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('eventousers.destroy', $user->pivot->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Está seguro de eliminar al usuario del evento?')" class="btn btn-link text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <hr>
                            @endif
                        @empty
                            <span>No hay ningún asistente.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer custom-header-footer">
            <a class="btn btn-primary" href="{{ route('events.index') }}">Volver</a>
        </div>
    </div>
</div>
@endsection