@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
    <header class="main-header">
        <h1>Gestión de Productos</h1>

        {{-- El botón "Añadir" solo se muestra si el usuario es admin --}}
        @if(auth()->user()->role === 'admin')
            <div class="actions-container">
                <a href="{{ route('products.create') }}" class="btn btn-primary">Añadir Nuevo Producto</a>
                <a href="{{ route('products.import') }}" class="btn btn-secondary">Importar CSV</a>
            </div>
        @endif
    </header>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="search-bar-wrapper">
        <form action="{{ route('products.index') }}" method="GET">
            {{-- CAMBIO AQUÍ --}}
            <input type="text" name="search" placeholder="Buscar por nombre, código o atributo..." value="{{ request('search') }}">
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
                    <th>Código</th>
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
                        <td class="{{ $product->quantity <= 3 ? 'low-stock' : '' }}">
                            {{ $product->quantity }}
                        </td>
                        <td>S/ {{ number_format($product->price, 2) }}</td>

                        {{-- ESTA ES LA CELDA CORREGIDA Y EN SU LUGAR CORRECTO --}}
                        <td>
                            <div class="actions-container">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-info">Ver</a>
{{--
                                @if(in_array(auth()->user()->role, ['admin', 'employee']))
                                    <form action="{{ route('products.sell', $product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">Vender</button>
                                    </form>
                                @endif
--}}
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

        @if($products->hasPages() || $products->total() > 0)
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    <span class="pagination-text">
                        Mostrando <strong>{{ $products->firstItem() ?? 0 }}</strong> a <strong>{{ $products->lastItem() ?? 0 }}</strong> de <strong>{{ $products->total() }}</strong> producto{{ $products->total() !== 1 ? 's' : '' }}
                    </span>
                    <form method="GET" action="{{ route('products.index') }}" class="pagination-per-page-form">
                        @foreach(request()->except(['per_page', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label for="per_page">Mostrar:</label>
                        <select id="per_page" name="per_page" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                </div>
                @if($products->hasPages())
                    @php
                        $current = $products->currentPage();
                        $last = $products->lastPage();
                        $range = 2; // páginas a cada lado del actual
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
                                @if($from > 2)<li><span class="pagination-ellipsis">…</span></li>@endif
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
                                @if($to < $last - 1)<li><span class="pagination-ellipsis">…</span></li>@endif
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
        .pagination-per-page-form { display: flex; align-items: center; gap: 8px; margin: 0; }
        .pagination-per-page-form label { color: #64748b; font-size: 14px; white-space: nowrap; }
        .pagination-per-page-form select { padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; background: #fff; cursor: pointer; }
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
