@extends('layouts.app')
@section('title', 'Editar Proveedor')
@section('content')
    <header class="main-header"><h1>Editar Proveedor: {{ $supplier->name }}</h1></header>
    <div class="content-wrapper">
        @if ($errors->any())
            {{-- ... error block ... --}}
        @endif
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group"><label for="name">Nombre del Proveedor</label><input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" required></div>
            <div class="form-group"><label for="contact_person">Persona de Contacto</label><input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"></div>
            <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}"></div>
            <div class="form-group"><label for="phone">Teléfono</label><input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}"></div>
            <div class="form-group"><label for="address">Dirección</label><textarea id="address" name="address">{{ old('address', $supplier->address) }}</textarea></div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">Actualizar Proveedor</button><a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancelar</a></div>
        </form>
    </div>
    <style>.form-group{margin-bottom:15px;display:flex;flex-direction:column}.form-group label{margin-bottom:5px;font-weight:600}.form-group input, .form-group textarea{width:100%;max-width:500px;padding:10px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box}.form-actions{margin-top:20px}</style>
@endsection
