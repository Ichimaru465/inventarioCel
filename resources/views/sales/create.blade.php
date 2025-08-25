@extends('layouts.app')
@section('title', 'Registrar Nueva Venta')

@section('content')
    <header class="main-header">
        <h1>Registrar Nueva Venta</h1>
    </header>

    <div class="content-wrapper"
        {{-- Inicializamos el componente Alpine.js --}}
        x-data="{
            search: '',
            searchResults: [],
            cart: [],
            loading: false,

            searchProducts() {
                if (this.search.length < 2) {
                    this.searchResults = [];
                    return;
                }
                this.loading = true;
                fetch(`/api/products/search?q=${this.search}`)
                    .then(res => res.json())
                    .then(data => {
                        this.searchResults = data;
                        this.loading = false;
                    });
            },

            addToCart(product) {
                const existingProduct = this.cart.find(item => item.id === product.id);
                if (existingProduct) {
                    existingProduct.quantity++;
                } else {
                    this.cart.push({ ...product, quantity: 1 });
                }
                this.search = '';
                this.searchResults = [];
            },

            removeFromCart(productId) {
                this.cart = this.cart.filter(item => item.id !== productId);
            },

            get total() {
                return this.cart.reduce((acc, item) => acc + (item.price * item.quantity), 0).toFixed(2);
            }
        }">

        @if ($errors->any())
            <div class="alert alert-danger" style="..."><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form action="{{ route('sales.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                {{-- Columna de Búsqueda y Carrito --}}
                <div class="form-column">
                    <div class="form-group">
                        <label for="search">Buscar Producto por Nombre o SKU</label>
                        <div style="position: relative;">
                            <input type="text" id="search" placeholder="Escribe para buscar..." class="search-input"
                                   x-model="search"
                                   @input.debounce.300ms="searchProducts()">

                            {{-- Resultados de la búsqueda --}}
                            <div x-show="searchResults.length > 0" class="search-results">
                                <ul>
                                    <template x-for="product in searchResults" :key="product.id">
                                        <li @click="addToCart(product)">
                                            <strong x-text="product.name"></strong><br>
                                            <small x-text="`SKU: ${product.sku} - Stock: ${product.quantity}`"></small>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Carrito de Venta --}}
                    <h3>Productos en la Venta</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unit.</th>
                                <th style="width: 100px;">Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="item.id">
                                <tr>
                                    <td>
                                        <span x-text="item.name"></span>
                                        {{-- Inputs ocultos para enviar los datos del carrito --}}
                                        <input type="hidden" :name="`products[${index}][product_id]`" :value="item.id">
                                    </td>
                                    <td x-text="`S/ ${parseFloat(item.price).toFixed(2)}`"></td>
                                    <td>
                                        <input type="number" :name="`products[${index}][quantity]`" x-model.number="item.quantity" min="1" :max="item.quantity" class="quantity-input">
                                    </td>
                                    <td x-text="`S/ ${(item.price * item.quantity).toFixed(2)}`"></td>
                                    <td>
                                        <button type="button" @click="removeFromCart(item.id)" class="btn-danger-sm">X</button>
                                    </td>
                                </tr>
                            </template>
                             <tr x-show="cart.length === 0">
                                <td colspan="5" style="text-align: center;">El carrito está vacío.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Columna de Resumen --}}
                <div class="form-column">
                    <div class="summary-card">
                        <h3>Resumen de la Venta</h3>
                        <div class="summary-total">
                            <span>TOTAL</span>
                            <span x-text="`S/ ${total}`"></span>
                        </div>
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
        .search-results { position: absolute; background: white; border: 1px solid #ccc; width: 100%; max-height: 200px; overflow-y: auto; z-index: 10; }
        .search-results ul { list-style: none; margin: 0; padding: 0; }
        .search-results li { padding: 10px; cursor: pointer; }
        .search-results li:hover { background-color: #f0f0f0; }
        .quantity-input { width: 80px; padding: 5px; text-align: center; }
        .btn-danger-sm { background-color: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; padding: 4px 8px; }
        .summary-card { background: #f8fafc; padding: 20px; border-radius: 8px; }
        .summary-total { display: flex; justify-content: space-between; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        .btn-lg { width: 100%; padding: 15px; font-size: 18px; }
    </style>
@endsection
