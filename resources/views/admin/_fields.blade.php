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
        <h2>{{ $modo }} Administrador</h2>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="row mb-3">
                <label for="name" class="col-md-4 col-form-label text-md-end">Nombre</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="name" id="name" value="{{ isset($admin->name) ? $admin->name : old('name') }}" required>
                </div>
            </div>
    
            <div class="row mb-3">
                <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>
                <div class="col-md-6">
                    <input type="email" class="form-control" name="email" id="email" value="{{ isset($admin->email) ? $admin->email : old('email') }}" required>
                </div>
            </div>
    
            <div class="row mb-3">
                <label for="password" class="col-md-4 col-form-label text-md-end">Contraseña</label>
                <div class="col-md-6">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" @if(!isset($admin)) required @endif>
    
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
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" @if(!isset($admin)) required @endif>
                </div>
            </div>

            @if(auth()->user()->is_super_admin)
            <div class="row mb-3">
                <label for="is_super_admin" class="col-md-4 col-form-label text-md-end">Super Administrador</label>
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_super_admin" id="is_super_admin" value="1" @if(isset($admin) && $admin->is_super_admin) checked @endif>
                        <label class="form-check-label" for="is_super_admin">Sí</label>
                    </div>
                </div>
            </div>
            @endif
    
        </div>
    </div>
    <div class="card-footer custom-header-footer">
        <div class="row mb-3">
            <div class="col text-left">
                <a class="btn btn-primary" href="{{ route('admins.index') }}">Volver</a>
            </div>
            <div class="col text-right">
                <input type="submit" class="btn btn-success" value="{{ $modo }} Administrador">
            </div>
        </div>
    </div>
</div>






