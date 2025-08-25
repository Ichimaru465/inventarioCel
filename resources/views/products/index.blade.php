@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
    <header class="main-header">
        <h1>Gestión de Productos</h1>

        {{-- El botón "Añadir" solo se muestra si el usuario es admin --}}
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-primary">Añadir Nuevo Producto</a>
        @endif
    </header>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="search-bar-wrapper">
        <form action="{{ route('products.index') }}" method="GET">
            <input type="text" name="search" placeholder="Buscar por nombre o SKU..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
    </div>
    <style>
        .search-bar-wrapper { background-color: white; padding: 15px; margin-top: -10px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .search-bar-wrapper form { display: flex; gap: 10px; }
        .search-bar-wrapper input { flex-grow: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
    </style>

    <div class="content-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>SKU</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Atributos</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                        <td>
                            @if ($product->attributes)
                                @foreach ($product->attributes as $key => $value)
                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}<br>
                                @endforeach
                            @endif
                        </td>
                        <td class="{{ $product->quantity <= 10 ? 'low-stock' : '' }}">
                            {{ $product->quantity }}
                        </td>
                        <td>S/ {{ number_format($product->price, 2) }}</td>

                        {{-- ESTA ES LA CELDA CORREGIDA Y EN SU LUGAR CORRECTO --}}
                        <td>
                            <div class="actions-container">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-info">Ver</a>

                                @if(in_array(auth()->user()->role, ['admin', 'employee']))
                                    <form action="{{ route('products.sell', $product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">Vender</button>
                                    </form>
                                @endif

                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary">Editar</a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
