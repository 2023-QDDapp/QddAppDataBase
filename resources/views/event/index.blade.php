@extends('layouts.private')

@section('title', "Qdd - Listado de eventos")

@section('content')
<div class="container">
    @if (Session::has('mensaje'))
        <br>
        <div class="alert alert-success">
            {{ Session::get('mensaje') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <strong>Eventos</strong>
    <a href="{{ route('events.create') }}" class="btn float-right"><i class="fas fa-plus text-success"></i></a>
    <hr>
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Categoría</th>
                <th>Organizador</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
            <tr>
                <td>{{$event->id}}</td>
                <td>{{$event->titulo}}</td>
                <td>{{$event->categoria->categoria}}</td>
                <td>{{$event->creador->nombre}}</td>
                <td>{{$event->fecha_hora_inicio}}</td>
                <td>{{$event->fecha_hora_fin}}</td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('events.show', $event->id) }}" class="btn"><i class="fas fa-eye text-primary"></i></a>
                        <a href="{{ route('events.edit', $event->id) }}" class="btn"><i class="fas fa-pencil-alt text-warning"></i></a>
                        <button type="button" class="btn" data-toggle="modal" data-target="#deleteEventModal{{ $event->id }}">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <!-- Modal de eliminación -->
            <div class="modal fade" id="deleteEventModal{{ $event->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteEventModal{{ $event->id }}Label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteEventModal{{ $event->id }}Label">Eliminar evento</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>¿Está seguro de que desea eliminar el evento <strong>#{{ $event->id }}</strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <form action="{{ route('events.destroy', $event->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>
@endsection




@section('datatable')
<!--Datatables-->
<script>
$(document).ready(function(){

	$('.data-table').DataTable( {
        "order": [[3, 'asc'], [0, 'desc']],
        "columnDefs": [
            { "bSortable": false, "aTargets": [ -1 ] },
        ],
		"pageLength": 10,
		"language": {
				"sProcessing":    "Procesando...",
				"sLengthMenu":    "Mostrar _MENU_ registros",
				"sZeroRecords":   "No se encontraron resultados",
				"sEmptyTable":    "Ningún dato disponible en esta tabla",
				"sInfo":          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				"sInfoEmpty":     "Mostrando registros del 0 al 0 de un total de 0 registros",
				"sInfoFiltered":  "(filtrado de un total de _MAX_ registros)",
				"sInfoPostFix":   "",
				"sSearch":        "Buscar:",
				"sUrl":           "",
				"sInfoThousands":  ",",
				"sLoadingRecords": "Cargando...",
				"oPaginate": {
					"sFirst":    "Primero",
					"sLast":    "Último",
					"sNext":    "Siguiente",
					"sPrevious": "Anterior"
				},
				"oAria": {
					"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
					"sSortDescending": ": Activar para ordenar la columna de manera descendente"
				}
        }
	});

});
</script>
@endsection