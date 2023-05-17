@extends('layouts.private')

@section('content')
<div class="container">
    <form action="{{ route('categorias.update', $categoria->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('categoria._fields', ['modo' => 'Editar'])

    </form>
</div>
@endsection