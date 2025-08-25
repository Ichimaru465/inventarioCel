@extends('layouts.app')
@section('title', 'Gestión de Usuarios')
@section('content')
    <header class="main-header">
        <h1>Gestión de Usuarios</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">Añadir Nuevo Usuario</a>
    </header>
    @if(session('success'))<div class="alert alert-success" style="...">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger" style="...">{{ session('error') }}</div>@endif
    <div class="content-wrapper">
        <table class="table">
            <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th></tr></thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            <div class="actions-container">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary">Editar</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align: center;">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 20px;">{{ $users->links() }}</div>
    </div>
@endsection
