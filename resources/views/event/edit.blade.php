@extends('layouts.private')

@section('content')
<div class="container">
    <form action="{{ route('events.update', $event->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('event._fields', ['modo' => 'Editar'])

    </form>
</div>
@endsection