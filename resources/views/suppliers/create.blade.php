@extends('layouts.app')
@section('title', 'Añadir Nuevo Proveedor')
@section('content')
    <header class="main-header"><h1>Añadir Nuevo Proveedor</h1></header>
    <div class="content-wrapper">
         @if ($errors->any())
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="form-group"><label for="name">Nombre del Proveedor</label><input type="text" id="name" name="name" value="{{ old('name') }}" required></div>
            <div class="form-group"><label for="contact_person">Persona de Contacto</label><input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}"></div>
            <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="{{ old('email') }}"></div>
            <div class="form-group"><label for="phone">Teléfono</label><input type="text" id="phone" name="phone" value="{{ old('phone') }}"></div>
            <div class="form-group"><label for="address">Dirección</label><textarea id="address" name="address">{{ old('address') }}</textarea></div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">Guardar Proveedor</button><a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancelar</a></div>
        </form>
    </div>
    <style>.form-group{margin-bottom:15px;display:flex;flex-direction:column}.form-group label{margin-bottom:5px;font-weight:600}.form-group input, .form-group textarea{width:100%;max-width:500px;padding:10px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box}.form-actions{margin-top:20px}</style>
@endsection
