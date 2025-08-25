@extends('layouts.app')

@section('title', 'Editar Categoría')

@section('content')
    <header class="main-header">
        <h1>Editar Categoría</h1>
    </header>

    <div class="content-wrapper">
        @if ($errors->any())
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Cambiar la acción y añadir el método PUT --}}
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Nombre de la Categoría</label>
                {{-- Rellenar el campo con los datos existentes --}}
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
            </div>

            <div class="form-group">
                <label>Atributos para esta Categoría</label>
                <div class="checkbox-grid">
                    @foreach ($attributes as $attribute)
                        <div class="checkbox-item">
                            {{--
                                Comprobamos si la categoría actual ($category) ya tiene este atributo ($attribute->id)
                                en su colección de atributos relacionados.
                            --}}
                            <input type="checkbox" name="attributes[]" value="{{ $attribute->id }}" id="attr-{{ $attribute->id }}"
                                   @if($category->attributes->contains($attribute->id)) checked @endif>
                            <label for="attr-{{ $attribute->id }}">{{ $attribute->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar Categoría</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    <style>
        .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }
        .checkbox-item { display: flex; align-items: center; }
        .checkbox-item input { margin-right: 8px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; max-width: 500px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-actions { margin-top: 20px; }
    </style>
@endsection
