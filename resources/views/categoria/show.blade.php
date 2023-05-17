@extends('layouts.private')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Datos del administrador</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Nombre:</strong>
                    {{ $admin->name }}
                </div>
                <div class="col-md-6">
                    <strong>Email:</strong>
                    {{ $admin->email }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <strong>Tipo de administrador:</strong>
                    @if ($admin->is_super_admin == 1)
                        Super Administrador
                    @else
                        Administrador básico
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a class="btn btn-primary" href="{{ route('admins.index') }}">Volver</a>
        </div>
    </div>
</div>
@endsection