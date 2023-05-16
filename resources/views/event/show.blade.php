@extends('layouts.private')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <strong>Datos del evento</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Título:</strong>
                            {{$event->titulo}}
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>organizador:</strong>
                            {{$event->creador->nombre}}
                        </div>
                        <div class="col-md-6">
                            <strong>Categoría:</strong>
                            {{$event->categoria->categoria}}
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
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <a class="btn btn-primary" href="{{ route('events.index') }}">Volver</a>
        </div>
    </div>
</div>
@endsection