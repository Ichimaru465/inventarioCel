@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('content')
    <header class="main-header"><h1>Editar Usuario: {{ $user->name }}</h1></header>
    <div class="content-wrapper">
        @if($errors->any())<div class="alert alert-danger" style="..."><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group"><label for="name">Nombre Completo</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required></div>
            <div class="form-group"><label for="email">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
            <div class="form-group"><label for="role">Rol</label><select name="role" required><option value="employee" @if(old('role', $user->role) === 'employee') selected @endif>Empleado</option><option value="admin" @if(old('role', $user->role) === 'admin') selected @endif>Administrador</option></select></div>
            <div class="form-group"><label for="password">Nueva Contraseña</label><input type="password" name="password"><small style="color:#6c757d;">Dejar en blanco para no cambiar la contraseña.</small></div>
            <div class="form-group"><label for="password_confirmation">Confirmar Nueva Contraseña</label><input type="password" name="password_confirmation"></div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">Actualizar Usuario</button><a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a></div>
        </form>
    </div>
    <style>.form-group{margin-bottom:15px;display:flex;flex-direction:column}.form-group label{margin-bottom:5px;font-weight:600}.form-group input, .form-group select{width:100%;max-width:500px;padding:10px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box}.form-actions{margin-top:20px}</style>
@endsection
