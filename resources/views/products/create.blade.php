@extends('layouts.app')

@section('title', 'Añadir Nuevo Producto')

@section('content')
    <header class="main-header">
        <h1>Añadir Nuevo Producto</h1>
    </header>

    <div class="content-wrapper">
        {{-- Bloque para mostrar errores de validación --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario con Alpine.js --}}
        <form action="{{ route('products.store') }}" method="POST"
              x-data="{
                  categoryId: '{{ old('category_id', '') }}',
                  attributes: [],
                  fetchAttributes() {
                      if (!this.categoryId) {
                          this.attributes = [];
                          return;
                      }
                      fetch(`/api/categories/${this.categoryId}/attributes`)
                          .then(response => response.json())
                          .then(data => {
                              this.attributes = data;
                          });
                  }
              }"
              x-init="fetchAttributes()">

            @csrf
            <div class="form-grid">
                {{-- Columna Izquierda --}}
                <div class="form-column">
                    <div class="form-group">
                        <label for="name">Nombre del Producto</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="sku">SKU</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}">
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea id="description" name="description">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Categoría</label>
                        <select id="category_id" name="category_id" x-model="categoryId" @change="fetchAttributes()" required>
                            <option value="">Seleccione una categoría</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="brand_id">Marca</label>
                        <select id="brand_id" name="brand_id">
                            <option value="">Seleccione una marca (opcional)</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                <div class="form-group">
                    <label for="supplier_id">Proveedor</label>
                    <select id="supplier_id" name="supplier_id">
                        <option value="">Seleccione un proveedor (opcional)</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                </div>

                {{-- Columna Derecha --}}
                <div class="form-column">
                    <div class="form-group">
                        <label for="price">Precio de Venta (S/)</label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="cost">Costo de Compra (S/)</label>
                        <input type="number" id="cost" name="cost" value="{{ old('cost') }}" step="0.01">
                    </div>

                    <div class="form-group">
                        <label for="quantity">Cantidad en Stock</label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                    </div>

                    {{-- Atributos dinámicos --}}
                    <fieldset class="attributes-fieldset">
                        <legend>Atributos</legend>

                        <template x-for="attribute in attributes" :key="attribute.id">
                            <div class="form-group">
                                <label x-text="attribute.name"></label>
                                <input type="text" :name="`attributes[${attribute.name}]`">
                            </div>
                        </template>

                        <template x-if="attributes.length === 0">
                            <p style="color: #6c757d;">Seleccione una categoría para ver sus atributos.</p>
                        </template>
                    </fieldset>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    {{-- Estilos del formulario --}}
    <style>
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .form-column { display: flex; flex-direction: column; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        .form-group label { margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        .attributes-fieldset { border: 1px solid #ccc; padding: 15px; border-radius: 4px; }
        .attributes-fieldset legend { font-weight: 600; padding: 0 10px; }
        .form-actions { margin-top: 20px; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
@endsection
