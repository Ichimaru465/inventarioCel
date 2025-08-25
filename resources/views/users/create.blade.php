@extends('layouts.app')
@section('title', 'Añadir Nuevo Usuario')
@section('content')
    <header class="main-header"><h1>Añadir Nuevo Usuario</h1></header>
    <div class="content-wrapper">
        @if($errors->any())<div class="alert alert-danger" style="..."><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="form-group"><label for="name">Nombre Completo</label><input type="text" name="name" value="{{ old('name') }}" required></div>
            <div class="form-group"><label for="email">Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
            <div class="form-group"><label for="role">Rol</label><select name="role" required><option value="employee" selected>Empleado</option><option value="admin">Administrador</option></select></div>
            <div class="form-group"><label for="password">Contraseña</label><input type="password" name="password" required></div>
            <div class="form-group"><label for="password_confirmation">Confirmar Contraseña</label><input type="password" name="password_confirmation" required></div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">Guardar Usuario</button><a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a></div>
        </form>
    </div>
    <style>.form-group{margin-bottom:15px;display:flex;flex-direction:column}.form-group label{margin-bottom:5px;font-weight:600}.form-group input, .form-group select{width:100%;max-width:500px;padding:10px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box}.form-actions{margin-top:20px}</style>
@endsection
