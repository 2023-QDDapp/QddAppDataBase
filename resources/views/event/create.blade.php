@extends('layouts.private')

@section('title', "Qdd - Crear evento")

@section('content')
<div class="container">
    <form action="{{ route('events.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @include('event._fields', ['modo' => 'Crear'])
    </form>
</div>
@endsection

