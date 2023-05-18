@extends('layouts.private')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header custom-header-footer">
            <strong>Datos del evento</strong>
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
                            <strong>Organizador:</strong>
                            {{$event->creador->nombre}}
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>Categoría:</strong>
                            <span class="badge badge-primary">{{$event->categoria->categoria}}</span>
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
        </div>
        <div class="card-footer custom-header-footer">
            <a class="btn btn-primary" href="{{ route('events.index') }}">Volver</a>
        </div>
    </div>
</div>
@endsection