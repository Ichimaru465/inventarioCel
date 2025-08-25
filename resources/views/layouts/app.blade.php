<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventario')</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body{margin:0;font-family:'Nunito',sans-serif;background-color:#f4f6f9;color:#333}.dashboard-container{display:flex}.sidebar{width:250px;background-color:#1e293b;color:white;min-height:100vh;display:flex;flex-direction:column}.sidebar-header{padding:20px;text-align:center;font-size:24px;font-weight:bold;border-bottom:1px solid #334155}.sidebar-nav{list-style:none;padding:20px 0;margin:0;flex-grow:1}.sidebar-nav a{display:block;color:#cbd5e1;text-decoration:none;padding:15px 20px;transition:background-color .3s,color .3s}.sidebar-nav a:hover,.sidebar-nav a.active{background-color:#334155;color:white}.logout-form{padding:20px}.logout-button{width:100%;background-color:#ef4444;color:white;border:none;padding:10px;border-radius:5px;cursor:pointer;font-size:16px}.logout-button:hover{background-color:#dc2626}.main-content{flex:1;padding:30px}.main-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.main-header h1{margin:0}.user-info{font-weight:600}.content-wrapper{background-color:white;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.05)}.table{width:100%;border-collapse:collapse;margin-top:20px}.table th,.table td{padding:12px;text-align:left;border-bottom:1px solid #e2e8f0}.table th{background-color:#f8fafc}.btn{text-decoration:none;padding:8px 12px;border-radius:5px;font-size:14px;border:none;cursor:pointer}.btn-primary{background-color:#3498db;color:white}.btn-secondary{background-color:#f6993f;color:white}.btn-danger{background-color:#e3342f;color:white}.low-stock{color:#e3342f;font-weight:bold}
        /* ... tus otros estilos ... */
        /* Contenedor para los botones de acción */
        .actions-container {
            display: flex;       /* ¡La magia de Flexbox! Pone los items en fila */
            align-items: center; /* Centra los items verticalmente si tienen alturas diferentes */
            gap: 6px;            /* Crea un espacio uniforme entre cada botón/formulario */
            flex-wrap: wrap;     /* Permite que bajen a la siguiente línea si no hay espacio */
        }

        /* Pequeño ajuste para que los formularios dentro del contenedor no añadan márgenes extra */
        .actions-container form {
            margin: 0;
        }
        /* ... dentro de la etiqueta <style> en layouts/app.blade.php ... */

        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle; /* <-- ¡AÑADE ESTA LÍNEA! */
        }

        .btn-info {
        background-color: #0ea5e9; /* Un azul cielo/cian */
            color: white;
        }
        .btn-info:hover {
            background-color: #0284c7; /* Un poco más oscuro al pasar el mouse */
        }
        /* ... dentro de tu etiqueta <style> ... */

/* --- NUEVOS ESTILOS PARA LA ZONA DE USUARIO --- */
.sidebar-user-panel {
    padding: 15px;
    border-bottom: 1px solid #334155;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.user-info .user-name {
    color: white;
    font-weight: 600;
    display: block;
}

.user-info .user-role {
    color: #94a3b8; /* Un gris azulado claro */
    font-size: 0.8em;
}

/* Ajustamos el botón de logout para que parezca un enlace/botón más pequeño */
.logout-link {
    background-color: #f34343;
    color: #e2e8f0;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    transition: background-color 0.2s;
}

.logout-link:hover {
    background-color: #ef4444; /* Rojo al pasar el mouse */
    color: white;
}
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
    <div class="sidebar-header">
        InventarioCel
    </div>

    {{-- NUEVA ZONA DE USUARIO --}}
    <div class="sidebar-user-panel">
        <div class="user-info">
            <span class="user-name">Hola, {{ Auth::user()->name }}</span>
            <span class="user-role">{{ ucfirst(Auth::user()->role) }}</span>
        </div>
        <div class="logout-form">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                {{-- Hemos cambiado el botón por un enlace para que sea más compacto --}}
                <button type="submit" class="logout-link">Salir</button>
            </form>
        </div>
    </div>

    {{-- Menú de Navegación Principal --}}
<ul class="sidebar-nav">
    {{-- ENLACES VISIBLES PARA TODOS (ADMIN Y EMPLEADO) --}}
    <li><a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}"><b>Registrar Venta</b></a></li>
    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
    <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Productos</a></li>

    {{-- ENLACES VISIBLES SOLO PARA EL ADMINISTRADOR --}}
    @if (auth()->user()->role === 'admin')
        <li><a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Categorías</a></li>
        <li><a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">Marcas</a></li>
        <li><a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">Proveedores</a></li>
        <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Usuarios</a></li>
    @endif
    </ul>
    </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
