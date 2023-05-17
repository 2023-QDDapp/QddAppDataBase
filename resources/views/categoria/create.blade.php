@extends('layouts.private')

@section('content')
<div class="container">
    <form action="{{ route('categorias.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @include('categoria._fields', ['modo' => 'Crear'])
    </form>
</div>
@endsection

