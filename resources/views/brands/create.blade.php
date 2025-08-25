@extends('layouts.app')
@section('title', 'Añadir Nueva Marca')
@section('content')
    <header class="main-header"><h1>Añadir Nueva Marca</h1></header>
    <div class="content-wrapper">
        <form action="{{ route('brands.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nombre de la Marca</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Marca</button>
                <a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    <style>.form-group{margin-bottom:15px;display:flex;flex-direction:column}.form-group label{margin-bottom:5px;font-weight:600}.form-group input{width:100%;max-width:500px;padding:10px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box}.form-actions{margin-top:20px}</style>
@endsection
