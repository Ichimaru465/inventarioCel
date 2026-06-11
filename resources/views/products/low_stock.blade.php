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

        @if($products->hasPages() || $products->total() > 0)
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    <span class="pagination-text">
                        Mostrando <strong>{{ $products->firstItem() ?? 0 }}</strong> a <strong>{{ $products->lastItem() ?? 0 }}</strong> de <strong>{{ $products->total() }}</strong> producto{{ $products->total() !== 1 ? 's' : '' }}
                    </span>
                </div>

                @if($products->hasPages())
                    @php
                        $current = $products->currentPage();
                        $last = $products->lastPage();
                        $range = 2;
                        $from = max(1, $current - $range);
                        $to = min($last, $current + $range);
                    @endphp

                    <nav class="pagination-nav" aria-label="Paginación">
                        <ul class="pagination-list">
                            <li>
                                @if($products->onFirstPage())
                                    <span class="pagination-btn pagination-btn--disabled" aria-disabled="true">‹ Anterior</span>
                                @else
                                    <a href="{{ $products->previousPageUrl() }}" class="pagination-btn" rel="prev">‹ Anterior</a>
                                @endif
                            </li>

                            @if($from > 1)
                                <li><a href="{{ $products->url(1) }}" class="pagination-btn">1</a></li>
                                @if($from > 2)
                                    <li><span class="pagination-ellipsis">...</span></li>
                                @endif
                            @endif

                            @for($page = $from; $page <= $to; $page++)
                                <li>
                                    @if($page == $current)
                                        <span class="pagination-btn pagination-btn--current" aria-current="page">{{ $page }}</span>
                                    @else
                                        <a href="{{ $products->url($page) }}" class="pagination-btn">{{ $page }}</a>
                                    @endif
                                </li>
                            @endfor

                            @if($to < $last)
                                @if($to < $last - 1)
                                    <li><span class="pagination-ellipsis">...</span></li>
                                @endif
                                <li><a href="{{ $products->url($last) }}" class="pagination-btn">{{ $last }}</a></li>
                            @endif

                            <li>
                                @if($products->hasMorePages())
                                    <a href="{{ $products->nextPageUrl() }}" class="pagination-btn" rel="next">Siguiente ›</a>
                                @else
                                    <span class="pagination-btn pagination-btn--disabled" aria-disabled="true">Siguiente ›</span>
                                @endif
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        @endif
    </div>

    <style>
        .pagination-wrapper { margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; }
        .pagination-info { display: flex; flex-wrap: wrap; align-items: center; gap: 16px; }
        .pagination-text { color: #64748b; font-size: 14px; }
        .pagination-nav { flex-shrink: 0; }
        .pagination-list { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; align-items: center; gap: 4px; }
        .pagination-list li { margin: 0; }
        .pagination-btn { display: inline-block; padding: 8px 12px; min-width: 40px; text-align: center; border-radius: 6px; font-size: 14px; text-decoration: none; color: #475569; border: 1px solid #e2e8f0; background: #fff; transition: background .15s, border-color .15s; }
        .pagination-btn:hover:not(.pagination-btn--disabled):not(.pagination-btn--current) { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
        .pagination-btn--current { background: #3b82f6; border-color: #3b82f6; color: #fff; font-weight: 600; cursor: default; }
        .pagination-btn--disabled { color: #94a3b8; background: #f8fafc; cursor: not-allowed; }
        .pagination-ellipsis { padding: 8px 6px; color: #94a3b8; font-size: 14px; user-select: none; }
    </style>
@endsection
