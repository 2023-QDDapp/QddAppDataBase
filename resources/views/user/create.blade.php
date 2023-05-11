@extends('layouts.private')

@section('content')
<div class="container">
    Crear administrador nuevo
    <hr>
    <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @include('user._fields', ['modo' => 'Crear'])
    </form>
</div>
@endsection

