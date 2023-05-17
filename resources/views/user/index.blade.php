@extends('layouts.private')

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
    <strong>Usuarios</strong>
    <a href="{{ route('users.create') }}" class="btn float-right"><i class="fas fa-plus text-success"></i></a>
    <hr>
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Edad</th>
                <th>Categorías</th>

                <th></th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->nombre}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->telefono}}</td>
                <td>{{$user->getAgeFromDate()}}</td>
                <td> 
                    @foreach ($user->categorias as $categoria)
                        <li>{{ $categoria->categoria }}</li>
                    @endforeach
                </td>

                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('users.show', $user->id) }}" class="btn"><i class="fas fa-eye text-primary"></i></a>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn"><i class="fas fa-pencil-alt text-warning"></i></a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="post">
                            @csrf
                            {{ method_field('DELETE') }}
                            <button type="submit" onclick="return confirm('¿Está seguro de que desea eliminar al usuario #{{ $user->id}}?')" class="btn">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
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

