@extends('layouts.private')

@section('content')

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header custom-header-footer">
            <h4><strong>Datos de usuario</strong></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto de usuario" class="rounded-circle" style="width: 150px; height: 150px;">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md">
                            <strong>Nombre:</strong>
                            {{$user->nombre}}
                        </div>
                        <div class="col-md">
                            <strong>Edad:</strong>
                            {{$user->getAgeFromDate()}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md">
                            <strong>Email:</strong>
                            {{$user->email}}
                        </div>
                        <div class="col-md">
                            <strong>Teléfono:</strong>
                            {{$user->telefono}}
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md">
                            <strong>Categorías:</strong>
                            <br>
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
                            <br>
                            {{$user->biografia}}
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row mt-3">
                <div class="col-md">
                    <strong>Eventos creados por el usuario:</strong>
                    <br>
                    @forelse ($user->eventosCreados as $evento)
                        <span class="badge badge-primary">{{ $evento->titulo }}</span>
                    @empty
                        <span>No ha creado eventos.</span>
                    @endforelse
                </div>
                <div class="col-md">
                    <strong>Eventos a los que asiste el usuario:</strong>
                    <br>
                    @forelse ($user->eventosAsistidos as $evento)
                        <span class="badge badge-primary">{{ $evento->titulo }}</span>
                    @empty
                        <span>No asiste a eventos.</span>
                    @endforelse
                </div>
            </div>

            <hr>
            
            <div class="row mt-3">
                <div class="col-md-7">
                    <h5><strong>Reseñas recibidas:</strong></h5>
                    
                    <div class="resenas-scroll" style="max-height: 400px; overflow: auto;">
                        @if ($mensajesRecibidos->isEmpty())
                            <p>No tiene reseñas.</p>
                        @else
                            @foreach ($mensajesRecibidos as $mensaje)
                                <div class="d-flex align-items-start mb-3">
                                    <div class="mr-3">
                                        <img src="{{ asset('storage/' . $mensaje->emisor->foto) }}" alt="{{ $mensaje->emisor->nombre }}" class="rounded-circle" style="width: 60px; height: 60px;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <p><strong>{{ $mensaje->emisor->nombre }}</strong></p>
                                        <p><em>{{ $mensaje->mensaje }}</em></p>
                                    </div>
                                    <div>
                                        <form action="{{ route('resenas.destroy', $mensaje->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Está seguro de que desea eliminar la reseña?')" class="btn btn-link text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="col-md-5">
                    <h5><strong>Usuarios seguidos:</strong></h5>
                    
                    <div class="follows-scroll" style="max-height: 400px; overflow: auto;">
                        @if ($user->follows->isEmpty())
                            <p>No sigue a ningún usuario.</p>
                        @else
                            @foreach ($user->follows as $followedUser)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <img src="{{ asset('storage/' . $followedUser->foto) }}" alt="{{ $followedUser->nombre }}" class="rounded-circle" style="width: 50px; height: 50px;">
                                    </div>
                                    <div>
                                        <p><strong>{{ $followedUser->nombre }}</strong></p>
                                    </div>
                                    <div>
                                        <form action="{{ route('follows.destroy', $followedUser->pivot->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('¿Está seguro de que desea dejar de seguir a este usuario?')" class="btn btn-link text-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer custom-header-footer">
            <a class="btn btn-primary" href="{{ route('users.index') }}">Volver</a>
        </div>
    </div>
</div>
@endsection