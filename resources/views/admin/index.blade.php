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
    <strong>Administradores</strong>
    @if(auth()->user()->is_super_admin)
    <a href="{{ route('admins.create') }}" class="btn float-right"><i class="fas fa-plus text-success"></i></a>
    @endif
    <hr>
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>nombre</th>
                <th>email</th>
                <th></th>
                
            </tr>
        </thead>
        <tbody>
            @foreach ($admins as $admin)
            <tr>
                <td>{{$admin->id}}</td>
                <td>{{$admin->name}}</td>
                <td>{{$admin->email}}</td>

                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('admins.show', $admin->id) }}" class="btn"><i class="fas fa-eye text-primary"></i></a>
                        <a href="{{ route('admins.edit', $admin->id) }}" class="btn"><i class="fas fa-pencil-alt text-warning"></i></a>
                        @if (auth()->user()->is_super_admin)
                            <form action="{{ route('admins.destroy', $admin->id) }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <button type="submit" onclick="return confirm('¿Está seguro de que desea eliminar el administrador #{{ $admin->id}}?')" class="btn">
                                    <i class="fas fa-trash-alt text-danger"></i>
                                </button>
                            </form>
                        @endif
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

