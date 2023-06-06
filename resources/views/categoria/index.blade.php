@extends('layouts.private')

@section('title', "Qdd - Listado de categorías")

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
    <strong>Categorías</strong>
    @if(auth()->user()->is_super_admin)
    <a href="{{ route('categorias.create') }}" class="btn float-right"><i class="fas fa-plus text-success"></i></a>
    @endif
    <hr>
    <table class="table data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Categoría</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categorias as $categoria)
            <tr>
                <td>{{$categoria->id}}</td>
                <td>{{$categoria->categoria}}</td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn"><i class="fas fa-pencil-alt text-warning"></i></a>
                        <button type="button" class="btn" data-toggle="modal" data-target="#deleteCategoriaModal{{ $categoria->id }}">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <!-- Modal de eliminación -->
            <div class="modal fade" id="deleteCategoriaModal{{ $categoria->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteCategoriaModal{{ $categoria->id }}Label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteCategoriaModal{{ $categoria->id }}Label">Eliminar categoría</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>¿Está seguro de que desea eliminar la categoría <strong>{{ $categoria->categoria }}</strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <form action="{{ route('categorias.destroy', $categoria->id) }}" method="post">
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
<!-- Datatables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('.data-table').DataTable({
        "order": [[1, 'asc']],
        "columnDefs": [
            { "bSortable": false, "targets": [-1] },
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