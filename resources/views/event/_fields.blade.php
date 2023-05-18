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
    <div class="card-header custom-header-footer">
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
                    <select name="user_id" id="users" class="js-example-basic-multiple" title="Selecciona el organizador" required>
                        <option value="" disabled selected>Selecciona organizador</option>
                        @isset($users)
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @if ($event->user_id == $user->id) selected @endif>{{ $user->nombre }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="imagen" class="col-md-4 col-form-label text-md-end">Imagen:</label>
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*" lang="es" onchange="updateFileName(this)">
                            <label class="custom-file-label" for="imagen" id="imagen-label">Seleccionar archivo</label>
                        </div>
                    </div>
                    @if(isset($event->imagen))
                        <div class="mt-3 text-center">
                            <img src="{{ asset('storage/' . $event->imagen) }}" alt="Imagen del evento" style="width: 250px;" class="rounded">
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="row mb-3">
                <label for="categorias" class="col-md-4 col-form-label text-md-end">Categoría:</label>
                <div class="col-md-6">
                    <select name="categoria_id" id="categorias" class="js-example-basic-multiple" title="Selecciona las categorías" required>
                        <option value="" disabled selected>Selecciona categoría</option>
                        @isset($categorias)
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @if ($event->categoria_id == $categoria->id) selected @endif>{{ $categoria->categoria }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="n_participantes" class="col-md-4 col-form-label text-md-end">Número de participantes:</label>
                <div class="col-md-6">
                    <input type="number" class="form-control" name="n_participantes" id="n_participantes" step="any" value="{{ isset($event->n_participantes) ? $event->n_participantes : old('n_participantes') }}" required>
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
                        <option disabled selected>Selecciona el tipo de evento</option>
                        <option value="privado" @if ($event->tipo == "privado") selected @endif>Privado</option>
                        <option value="público" @if ($event->tipo == "público") selected @endif>Público</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="fecha_hora_inicio" class="col-md-4 col-form-label text-md-end">Fecha y hora inicio</label>
                <div class="col-md-6">
                    <input type="datetime-local" class="form-control" name="fecha_hora_inicio" id="fecha_hora_inicio" value="{{ isset($event->fecha_hora_inicio) ? date('Y-m-d\TH:i', strtotime($event->fecha_hora_inicio)) : old('fecha_hora_inicio') }}" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label for="fecha_hora_fin" class="col-md-4 col-form-label text-md-end">Fecha y hora fin</label>
                <div class="col-md-6">
                    <input type="datetime-local" class="form-control" name="fecha_hora_fin" id="fecha_hora_fin" value="{{ isset($event->fecha_hora_fin) ? date('Y-m-d\TH:i', strtotime($event->fecha_hora_fin)) : old('fecha_hora_fin') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="location" class="col-md-4 col-form-label text-md-end">Localización</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="location" id="location" value="{{ isset($event->location) ? $event->location : old('location') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="latitud" class="col-md-4 col-form-label text-md-end">Latitud:</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="latitud" id="latitud" step="any" value="{{ isset($event->latitud) ? $event->latitud : old('latitud') }}" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <label for="longitud" class="col-md-4 col-form-label text-md-end">Longitud:</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="longitud" id="longitud" step="any" value="{{ isset($event->longitud) ? $event->longitud : old('longitud') }}" required>
                </div>
            </div>

        </div>
    </div>
    
    <div class="card-footer custom-header-footer">
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
