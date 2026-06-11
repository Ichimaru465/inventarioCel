@extends('layouts.app')

@section('title', 'Productos Bajo Stock')

@section('content')
    <header class="main-header">
        <h1>Productos Bajo Stock (&lt;= 3)</h1>
        <div class="actions-container">
            <a href="{{ route('products.low-stock.download') }}" class="btn btn-info">Descargar CSV</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Volver a Productos</a>
        </div>
    </header>

    <div class="content-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Proveedor</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->sku ?? '-' }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? 'Sin categoría' }}</td>
                        <td>{{ $product->brand->name ?? '-' }}</td>
                        <td>{{ $product->supplier->name ?? '-' }}</td>
                        <td class="low-stock">{{ $product->quantity }}</td>
                        <td>S/ {{ number_format($product->price, 2) }}</td>
                        <td>
                            <div class="actions-container">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-info">Ver</a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;color:#64748b;">No hay productos con bajo stock.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $products->links() }}
        </div>
    </div>
@endsection
