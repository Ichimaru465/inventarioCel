@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@section('content')
    <header class="main-header">
        <h1>Gestión de Proveedores</h1>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Añadir Nuevo Proveedor</a>
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
                    <th>Contacto</th>
                    <th>Nº de Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>
                            @if($supplier->contact_person)
                                {{ $supplier->contact_person }}<br>
                            @endif
                            @if($supplier->email)
                                <small style="color: #6c757d;">{{ $supplier->email }}</small>
                            @endif
                        </td>
                        <td>{{ $supplier->products_count }}</td>
                        <td>
                            <div class="actions-container">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-secondary">Editar</a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este proveedor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay proveedores registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $suppliers->links() }}
        </div>
    </div>
@endsection
