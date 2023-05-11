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

    <div class="row mb-3">
        <label for="foto" class="col-md-4 col-form-label text-md-end">Foto</label>

        <div class="col-md-6">
            <input type="file" class="form-control" name="foto" id="foto" accept="image/*">
        </div>
    </div>

</div>

<hr>

<div class="row mb-3">
    <div class="col text-center">
        <input type="submit" class="btn btn-primary" value="{{ $modo }} Administrador">
    </div>
</div>
<div class="row mb-3">
    <div class="col text-center">
        <a class="btn btn-success" href="{{ route('admins.index') }}" >volver</a>
    </div>
</div>