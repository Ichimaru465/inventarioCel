@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
    <header class="main-header">
        <h1>{{ $product->name }}</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Volver al listado</a>
    </header>

    <div class="content-wrapper">
        <div class="product-details-grid">
            <div class="detail-item">
                <span class="detail-label">ID</span>
                <span class="detail-value">{{ $product->id }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">SKU</span>
                <span class="detail-value">{{ $product->sku }}</span>
            </div>
             <div class="detail-item">
                <span class="detail-label">Categoría</span>
                <span class="detail-value">{{ $product->category->name ?? 'N/A' }}</span>
            </div>
             <div class="detail-item">
                <span class="detail-label">Marca</span>
                <span class="detail-value">{{ $product->brand->name ?? 'N/A' }}</span>
            </div>
             <div class="detail-item">
                <span class="detail-label">Proveedor</span>
                <span class="detail-value">{{ $product->supplier->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Precio de Venta</span>
                <span class="detail-value">S/ {{ number_format($product->price, 2) }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Costo de Compra</span>
                <span class="detail-value">S/ {{ number_format($product->cost, 2) }}</span>
            </div>
             <div class="detail-item">
                <span class="detail-label">Stock Actual</span>
                <span class="detail-value" style="font-weight: bold; font-size: 1.2em;">{{ $product->quantity }}</span>
            </div>
             <div class="detail-item detail-full-width">
                <span class="detail-label">Descripción</span>
                <p class="detail-value">{{ $product->description ?? 'Sin descripción.' }}</p>
            </div>
             <div class="detail-item detail-full-width">
                <span class="detail-label">Atributos</span>
                <div class="detail-value attributes-list">
                    @if($product->attributes)
                        @foreach($product->attributes as $key => $value)
                            <span><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</span>
                        @endforeach
                    @else
                        <span>No hay atributos definidos.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .product-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .detail-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
        }
        .detail-label {
            display: block;
            font-size: 0.9em;
            color: #64748b;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 1.1em;
        }
        .detail-full-width {
            grid-column: 1 / -1;
        }
        .attributes-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .attributes-list span {
            background-color: #e2e8f0;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
    </style>
@endsection
