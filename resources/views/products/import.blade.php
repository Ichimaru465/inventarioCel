@extends('layouts.app')
@section('title', 'Importar productos (CSV)')

@section('content')
    <header class="main-header">
        <h1>Importar productos desde CSV</h1>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Volver a Productos</a>
    </header>

    <div class="content-wrapper">
        @if(session('error'))
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <p style="margin-bottom: 20px;">
            Sube un archivo CSV con las columnas indicadas abajo. Puedes descargar la plantilla de ejemplo para ver el formato.
        </p>

        <div style="margin-bottom: 24px;">
            <a href="{{ route('products.import.template') }}" class="btn btn-info">Descargar plantilla CSV</a>
        </div>

        <form action="{{ route('products.import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="csv">Archivo CSV</label>
                <input type="file" name="csv" id="csv" accept=".csv,.txt" required class="form-control" style="padding: 8px;">
                @error('csv')
                    <span class="text-danger" style="font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Importar</button>
        </form>

        <div style="margin-top: 30px; padding: 15px; background: #f8fafc; border-radius: 8px;">
            <h3 style="margin-top: 0;">Columnas del CSV</h3>
            <table class="table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>Columna</th>
                        <th>Obligatorio</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>name</code></td><td>Sí</td><td>Nombre del producto</td></tr>
                    <tr><td><code>sku</code></td><td>No</td><td>Código (puede repetirse)</td></tr>
                    <tr><td><code>description</code></td><td>No</td><td>Descripción</td></tr>
                    <tr><td><code>price</code></td><td>Sí</td><td>Precio (número, ej. 10.50)</td></tr>
                    <tr><td><code>cost</code></td><td>No</td><td>Costo</td></tr>
                    <tr><td><code>quantity</code></td><td>No</td><td>Stock inicial (default 0)</td></tr>
                    <tr><td><code>category_id</code> o <code>category</code></td><td>Sí</td><td>ID de categoría o nombre; si no existe, se crea</td></tr>
                    <tr><td><code>brand_id</code> o <code>brand</code></td><td>No</td><td>ID o nombre de marca; si no existe, se crea</td></tr>
                    <tr><td><code>supplier_id</code> o <code>supplier</code></td><td>No</td><td>ID o nombre de proveedor; si no existe, se crea</td></tr>
                    <tr><td><code>attributes</code></td><td>No</td><td>JSON, ej. {"color":"Rojo","talla":"M"}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
