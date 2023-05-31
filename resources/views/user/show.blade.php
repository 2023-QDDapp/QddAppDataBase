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
                                        <!-- Botón de eliminar con modal -->
                                        <button type="button" class="btn btn-link text-danger" data-toggle="modal" data-target="#deleteReviewModal{{ $mensaje->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
            
                                        <!-- Modal de confirmación de eliminación -->
                                        <div class="modal fade" id="deleteReviewModal{{ $mensaje->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteReviewModalLabel{{ $mensaje->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteReviewModalLabel{{ $mensaje->id }}">Confirmar eliminación</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>¿Está seguro de que desea eliminar la reseña?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('resenas.destroy', $mensaje->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                        @if ($followingUsers->isEmpty())
                            <p>No sigue a ningún usuario.</p>
                        @else
                            @foreach ($followingUsers as $followedUser)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="mr-3">
                                        <img src="{{ asset('storage/' . $followedUser->foto) }}" alt="{{ $followedUser->nombre }}" class="rounded-circle" style="width: 50px; height: 50px;">
                                    </div>
                                    <div>
                                        <p><strong>{{ $followedUser->nombre }}</strong></p>
                                    </div>
                                    <div>
                                        <!-- Botón de eliminar con modal -->
                                        <button type="button" class="btn btn-link text-danger" data-toggle="modal" data-target="#unfollowUserModal{{ $followedUser->pivot->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                
                                        <!-- Modal de confirmación de eliminación -->
                                        <div class="modal fade" id="unfollowUserModal{{ $followedUser->pivot->id }}" tabindex="-1" role="dialog" aria-labelledby="unfollowUserModalLabel{{ $followedUser->pivot->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="unfollowUserModalLabel{{ $followedUser->pivot->id }}">Confirmar eliminación</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>¿Eliminar seguidor usuario de la lista de seguidores?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('follows.destroy', $followedUser->pivot->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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