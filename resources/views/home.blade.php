@extends('layouts.private')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h2>{{ __('¡Bienvenido!') }}</h2>
                </div>

                <div class="card-body text-center">
                    <img src="{{ asset('img/logo.png') }}" alt="Logotipo de la empresa" width="200">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="font-weight-bold">{{ __('¡Iniciaste sesión') }} {{ Auth::user()->name }}!</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
