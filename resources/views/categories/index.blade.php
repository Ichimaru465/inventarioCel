@extends('layouts.app')

@section('title', 'Gestión de Categorías')

@section('content')
    <header class="main-header">
        <h1>Gestión de Categorías</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Añadir Nueva Categoría</a>
    </header>

    <div class="content-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Nº de Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            <div class="actions-container">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary">Editar</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('¿Estás seguro? Eliminar una categoría no eliminará sus productos, pero los dejará sin categoría.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay categorías registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $categories->links() }}
        </div>
    </div>
@endsection
