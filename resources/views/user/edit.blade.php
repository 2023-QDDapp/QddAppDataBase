@extends('layouts.private')

@section('content')
<div class="container">
    <strong>Actualizar datos de administrador</strong>
    <form action="{{ route('admins.update', $admin->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin._fields', ['modo' => 'Editar'])

    </form>
</div>
@endsection