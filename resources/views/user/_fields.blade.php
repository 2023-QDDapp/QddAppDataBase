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
        <h2>{{ $modo }} Usuario</h2>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="row mb-3">
                <label for="nombre" class="col-md-4 col-form-label text-md-end">Nombre</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="nombre" id="nombre" value="{{ isset($user->nombre) ? $user->nombre : old('nombre') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="fecha_nacimiento" class="col-md-4 col-form-label text-md-end">Fecha de Nacimiento</label>

                <div class="col-md-6">
                    <input type="date" class="form-control" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ isset($user->fecha_nacimiento) ? $user->fecha_nacimiento : old('fecha_nacimiento') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="telefono" class="col-md-4 col-form-label text-md-end">Teléfono</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="telefono" id="telefono" value="{{ isset($user->telefono) ? $user->telefono : old('telefono') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>
                <div class="col-md-6">
                    <input type="email" class="form-control" name="email" id="email" value="{{ isset($user->email) ? $user->email : old('email') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <label for="foto" class="col-md-4 col-form-label text-md-end">Foto</label>
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*" lang="es" onchange="updateFileName(this)">
                            <label class="custom-file-label" for="foto" id="foto-label">Seleccionar archivo</label>
                        </div>
                    </div>
                    @if (isset($user->foto))
                        <div class="mt-3 text-center">
                            <img src="{{ asset('storage/' . $user->foto) }}" alt="Imagen existente" style="width: 150px; height: 150px;" class="rounded-circle">
                            <input type="hidden" name="foto_old" value="{{ $user->foto }}">
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <label for="biografia" class="col-md-4 col-form-label text-md-end">Biografía</label>

                <div class="col-md-6">
                    <textarea class="form-control" name="biografia" id="biografia" rows="4" required>{{ isset($user->biografia) ? $user->biografia : old('biografia') }}</textarea>
                </div>
            </div>

            <div class="row mb-3">
                <label for="password" class="col-md-4 col-form-label text-md-end">Contraseña</label>

                <div class="col-md-6">
                @if(!isset($user))
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" required>
                    @else
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                    @endif

                    @error('password') 
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Confirmar contraseña</label>

                <div class="col-md-6">
                    @if(!isset($user))
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" required>
                    @else
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                    @endif
                </div>
            </div>

        </div>
    </div>
    
    <div class="card-footer">
        <div class="row mb-3">
            <div class="col text-left">
                <a class="btn btn-primary" href="{{ route('users.index') }}">Volver</a>
            </div>
            <div class="col text-right">
                <input type="submit" class="btn btn-success" value="{{ $modo }} usuario">
            </div>
        </div>
    </div>

    
</div>

<script>
    function updateFileName(input) {
        var fileName = input.files[0].name;
        document.getElementById('foto-label').textContent = fileName;
    }
</script>