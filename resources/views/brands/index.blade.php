@extends('layouts.app')

@section('title', 'Gestión de Marcas')

@section('content')
    <header class="main-header">
        <h1>Gestión de Marcas</h1>
        <a href="{{ route('brands.create') }}" class="btn btn-primary">Añadir Nueva Marca</a>
    </header>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

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
                @forelse ($brands as $brand)
                    <tr>
                        <td>{{ $brand->id }}</td>
                        <td>{{ $brand->name }}</td>
                        <td>{{ $brand->products_count }}</td>
                        <td>
                            <div class="actions-container">
                                <a href="{{ route('brands.edit', $brand) }}" class="btn btn-secondary">Editar</a>
                                <form action="{{ route('brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta marca?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay marcas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $brands->links() }}
        </div>
    </div>
@endsection
