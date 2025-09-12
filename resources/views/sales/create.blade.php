@extends('layouts.app')
@section('title', 'Registrar Nueva Venta')

@section('content')
    <header class="main-header">
        <h1>Registrar Nueva Venta</h1>
    </header>

    <div class="content-wrapper"
        x-data="{
            search: '',
            searchResults: [],
            cart: [],
            loading: false,
            discountPercent: 0,
            discountFixed: 0,

            searchProducts() { if (this.search.length < 2) { this.searchResults = []; return; } this.loading = true; fetch(`/api/products/search?q=${this.search}`).then(res => res.json()).then(data => { this.searchResults = data; this.loading = false; }); },
            addToCart(product) { const existing = this.cart.find(i => i.id === product.id); if (existing) { existing.quantity++; } else { this.cart.push({ ...product, quantity: 1 }); } this.search = ''; this.searchResults = []; },
            removeFromCart(productId) { this.cart = this.cart.filter(i => i.id !== productId); },

            get subtotal() { return this.cart.reduce((acc, item) => acc + (item.price * item.quantity), 0); },
            get discountAmount() { let discount = 0; if (this.discountPercent > 0) { discount = (this.subtotal * this.discountPercent) / 100; } else if (this.discountFixed > 0) { discount = this.discountFixed; } return Math.min(discount, this.subtotal); },
            get grandTotal() { return this.subtotal - this.discountAmount; }
        }">

        @if ($errors->any())
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form action="{{ route('sales.store') }}" method="POST">
            @csrf
            <input type="hidden" name="discount_percent" :value="discountPercent">
            <input type="hidden" name="discount_fixed" :value="discountFixed">

            <div class="form-grid">
                <div class="form-column">
                    <div class="form-group">
                        <label for="search">Buscar Producto por Nombre o Atributo</label>
                        <div style="position: relative;">
                            <input type="text" id="search" placeholder="Escribe para buscar..." class="search-input"
                                   x-model="search"
                                   @input.debounce.300ms="searchProducts()">

                            <div x-show="searchResults.length > 0" class="search-results" x-cloak>
                                <ul>
                                    <template x-for="product in searchResults" :key="product.id">
                                        <li @click="addToCart(product)">
                                            <strong x-text="product.name"></strong>
                                            <div class="search-result-attributes">
                                                <template x-for="([key, value]) in Object.entries(product.attributes || {})" :key="key">
                                                    <span x-show="value" class="attribute-tag">
                                                        <span x-text="value"></span>
                                                    </span>
                                                </template>
                                            </div>
                                            <small x-text="`SKU: ${product.sku} - Stock: ${product.quantity}`"></small>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h3>Productos en la Venta</h3>
                    <table class="table">
                        <thead><tr><th>Producto</th><th>Precio Unit.</th><th style="width: 100px;">Cantidad</th><th>Subtotal</th><th></th></tr></thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="item.id">
                                <tr>
                                    <td>
                                        <span x-text="item.name"></span>
                                        {{-- NUEVO: Mostrar atributos del producto en el carrito --}}
                                        <div class="cart-item-attributes">
                                            <template x-for="([key, value]) in Object.entries(item.attributes || {})">
                                                <span x-show="value" class="attribute-tag-sm">
                                                    <strong x-text="`${key.replace('_', ' ')}:`"></strong>
                                                    <span x-text="value" style="margin-left: 3px;"></span>
                                                </span>
                                            </template>
                                        </div>
                                        <input type="hidden" :name="`products[${index}][product_id]`" :value="item.id">
                                    </td>
                                    <td x-text="`S/ ${parseFloat(item.price).toFixed(2)}`"></td>
                                    <td><input type="number" :name="`products[${index}][quantity]`" x-model.number="item.quantity" min="1" :max="item.quantity" class="quantity-input"></td>
                                    <td x-text="`S/ ${(item.price * item.quantity).toFixed(2)}`"></td>
                                    <td><button type="button" @click="removeFromCart(item.id)" class="btn-danger-sm">X</button></td>
                                </tr>
                            </template>
                             <tr x-show="cart.length === 0"><td colspan="5" style="text-align: center;">El carrito está vacío.</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Columna de Resumen --}}
                <div class="form-column">
                    <div class="summary-card">
                        <h3>Resumen de la Venta</h3>
                        <div class="summary-item"><span>Subtotal</span><span x-text="`S/ ${subtotal.toFixed(2)}`"></span></div>
                        <div class="form-group" style="margin-top: 15px;"><label>Descuento (%)</label><input type="number" step="0.01" min="0" x-model.number="discountPercent" @input="discountFixed = 0" class="discount-input"></div>
                        <div class="form-group"><label>Descuento Fijo (S/)</label><input type="number" step="0.01" min="0" x-model.number="discountFixed" @input="discountPercent = 0" class="discount-input"></div>
                        <div class="summary-item" x-show="discountAmount > 0"><span >Descuento</span><span x-text="`- S/ ${discountAmount.toFixed(2)}`" style="color: #ef4444;"></span></div>
                        <hr>
                        <div class="summary-total"><span>TOTAL A PAGAR</span><span x-text="`S/ ${grandTotal.toFixed(2)}`"></span></div>
                        <button type="submit" class="btn btn-primary btn-lg" :disabled="cart.length === 0">Completar Venta</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Estilos para esta página --}}
    <style>
        .form-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .search-input { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; }
        .search-results { position: absolute; background: white; border: 1px solid #ccc; border-top: none; width: 100%; max-height: 250px; overflow-y: auto; z-index: 10; border-radius: 0 0 4px 4px; }
        .search-results ul { list-style: none; margin: 0; padding: 0; }
        .search-results li { padding: 10px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
        .search-results li:last-child { border-bottom: none; }
        .search-results li:hover { background-color: #f0f0f0; }
        .quantity-input { width: 80px; padding: 5px; text-align: center; }
        .btn-danger-sm { background-color: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; padding: 4px 8px; }
        .summary-card { background: #f8fafc; padding: 20px; border-radius: 8px; }
        .summary-total { display: flex; justify-content: space-between; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        .btn-lg { width: 100%; padding: 15px; font-size: 18px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .discount-input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 100%; box-sizing: border-box; }
        hr { border: none; border-top: 1px solid #e2e8f0; margin: 15px 0; }
        .search-result-attributes { display: flex; flex-wrap: wrap; gap: 5px; margin: 4px 0; }
        .attribute-tag { background-color: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 10px; font-size: 0.8em; font-weight: 600; }

        /* NUEVOS ESTILOS PARA ATRIBUTOS EN EL CARRITO */
        .cart-item-attributes { margin-top: 5px; }
        .attribute-tag-sm {
            background-color: #f1f5f9; /* Un gris más claro */
            color: #64748b;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 0.75em; /* Más pequeño */
            font-weight: 600;
            display: inline-block;
            margin-right: 4px;
            margin-bottom: 4px;
        }
        .attribute-tag-sm strong { text-transform: capitalize; }
    </style>
@endsection

