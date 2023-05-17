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
        <h2>{{ $modo }} Categoría</h2>
    </div>
    <div class="card-body">
        <div class="form-group">

            <div class="row mb-3">
                <label for="name" class="col-md-4 col-form-label text-md-end">Categoria</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="categoria" id="categoria" value="{{ isset($categoria->categoria) ? $categoria->categoria : old('categoria') }}" required>
                </div>
            </div>
    
        </div>
    </div>
    <div class="card-footer">
        <div class="row mb-3">
            <div class="col text-left">
                <a class="btn btn-primary" href="{{ route('categorias.index') }}">Volver</a>
            </div>
            <div class="col text-right">
                <input type="submit" class="btn btn-success" value="{{ $modo }} Categoria">
            </div>
        </div>
    </div>
</div>






