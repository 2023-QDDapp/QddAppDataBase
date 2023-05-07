@extends('layouts.private')

@section('content')
<div class="container">
    Crear administrador nuevo
    <hr>
    <form action="{{ route('admins.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @include('admin._fields', ['modo' => 'Crear'])
    </form>
</div>
@endsection

