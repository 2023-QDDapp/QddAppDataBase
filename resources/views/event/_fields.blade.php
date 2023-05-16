<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


@if(count($errors) > 0)
    <div class="alert alert-danger" role="alert">
        <ul>
        @foreach ($errors->all() as $error)
            <li>
                {{ $error }}
            </li>
        @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h2>{{ $modo }} Evento</h2>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="row mb-3">
                <label for="titulo" class="col-md-4 col-form-label text-md-end">Título del evento</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="titulo" id="titulo" value="{{ isset($event->titulo) ? $event->titulo : old('titulo') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="usuarios" class="col-md-4 col-form-label text-md-end">Organizador:</label>
                <div class="col-md-6">
                    <select name="users[]" id="users" class="js-example-basic-multiple" title="Selecciona los usuarios" required>
                        <option value="" disabled selected>Selecciona organizador</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @if ($event->user_id == $user->id) selected @endif>{{ $user->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="categorias" class="col-md-4 col-form-label text-md-end">Categoría:</label>
                <div class="col-md-6">
                    <select name="categorias[]" id="categorias" class="js-example-basic-multiple" title="Selecciona las categorías" required>
                        <option value="" disabled selected>Selecciona categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @if ($event->categoria_id == $categoria->id) selected @endif>{{ $categoria->categoria }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <label for="descripcion" class="col-md-4 col-form-label text-md-end">Descripción del evento</label>

                <div class="col-md-6">
                    <textarea class="form-control" name="descripcion" id="descripcion" rows="4" required>{{ isset($event->descripcion) ? $event->descripcion : old('descripcion') }}</textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="tipo" class="col-md-4 col-form-label text-md-end">Tipo de evento:</label>
                <div class="col-md-6">
                    <select name="tipo" id="tipo" class="js-example-basic-multiple" title="Selecciona el tipo de evento" required>
                        <option selected>seleciona el tipo de evento</option>
                        <option value="privado">privado</option>
                        <option value="público">público</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="fecha" class="col-md-4 col-form-label text-md-end">Fecha inicio</label>
                <div class="col-md-6">
                    <input type="date" class="form-control" name="fecha" id="fecha" value="{{ isset($event->fecha_hora_inicio) ? $event->fecha_hora_inicio : old('fecha_hora_inicio') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="fecha" class="col-md-4 col-form-label text-md-end">Fecha fin</label>
                <div class="col-md-6">
                    <input type="date" class="form-control" name="fecha" id="fecha" value="{{ isset($event->fecha_hora_fin) ? $event->fecha_hora_fin : old('fecha_hora_fin') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="location" class="col-md-4 col-form-label text-md-end">Localización</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="location" id="location" value="{{ isset($event->location) ? $event->location : old('location') }}" required>
                </div>
            </div>

        </div>
    </div>
    
    <div class="card-footer">
        <div class="row mb-3">
            <div class="col text-left">
                <a class="btn btn-primary" href="{{ route('events.index') }}">Volver</a>
            </div>
            <div class="col text-right">
                <input type="submit" class="btn btn-success" value="{{ $modo }} evento">
            </div>
        </div>
    </div>

    
</div>

<script>
    function updateFileName(input) {
        var fileName = input.files[0].name;
        document.getElementById('foto-label').textContent = fileName;
    }
    //Extensión de la funcionalidad de javascript para SELECT2
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });

    /*flatpickr("#fecha", {
        enableTime: false,
        dateFormat: "Y-m-d",
    });*/

</script>
