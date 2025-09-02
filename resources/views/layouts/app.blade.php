<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Accesorios Ramirez')</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Estilo para que Alpine.js no muestre elementos antes de cargar */
        [x-cloak] { display: none !important; }

        /* Estilos Generales */
        body{margin:0;font-family:'Nunito',sans-serif;background-color:#f4f6f9;color:#333}
        .dashboard-container{display:flex;min-height:100vh}
        .main-content{flex:1;padding:30px}
        .main-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
        .main-header h1{margin:0}
        .content-wrapper{background-color:white;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.05)}

        /* Estilos de Tabla */
        .table{width:100%;border-collapse:collapse;margin-top:20px}
        .table th,.table td{padding:12px;text-align:left;border-bottom:1px solid #e2e8f0;vertical-align:middle}
        .table th{background-color:#f8fafc}
        .low-stock{color:#e3342f;font-weight:bold}

        /* Estilos de Botones */
        .btn{text-decoration:none;padding:8px 12px;border-radius:5px;font-size:14px;border:none;cursor:pointer;transition:background-color .2s}
        .btn-primary{background-color:#3b82f6;color:white}.btn-primary:hover{background-color:#2563eb}
        .btn-secondary{background-color:#f97316;color:white}.btn-secondary:hover{background-color:#ea580c}
        .btn-danger{background-color:#ef4444;color:white}.btn-danger:hover{background-color:#dc2626}
        .btn-info{background-color:#0ea5e9;color:white}.btn-info:hover{background-color:#0284c7}
        .actions-container{display:flex;align-items:center;gap:6px;flex-wrap:wrap}.actions-container form{margin:0}

        /* Estilos de Barra Lateral */
        .sidebar{width:250px;background-color:#1e293b;color:white;display:flex;flex-direction:column;transition:transform 0.3s ease-in-out;flex-shrink:0;}
        .sidebar-header{padding:20px;text-align:center;border-bottom:1px solid #334155;}
        .app-logo{max-height:150px;width:auto;}
        .sidebar-user-panel{padding:15px;border-bottom:1px solid #334155;display:flex;justify-content:space-between;align-items:center}
        .user-info .user-name{color:white;font-weight:600;display:block}.user-info .user-role{color:#94a3b8;font-size:.8em}
        .logout-link{background-color:#334155;color:#e2e8f0;border:none;padding:6px 12px;border-radius:5px;cursor:pointer;font-size:14px;text-decoration:none;transition:background-color .2s}
        .logout-link:hover{background-color:#ef4444;color:white}
        .sidebar-nav{list-style:none;padding:20px 0;margin:0;flex-grow:1}
        .sidebar-nav a{display:block;color:#cbd5e1;text-decoration:none;padding:15px 20px;transition:background-color .3s,color .3s}
        .sidebar-nav a:hover,.sidebar-nav a.active{background-color:#334155;color:white}

        /* Estilos Responsive */
        .mobile-header { display: none; }
        .sidebar-overlay { display: none; }

        @media (max-width: 768px) {
            .dashboard-container { flex-direction: column; }
            .sidebar {
                position: fixed; left: 0; top: 0; bottom: 0;
                height: 100vh;
                transform: translateX(-100%);
                z-index: 1000;
            }
            .sidebar.is-open { transform: translateX(0); }
            .sidebar-overlay {
                display: block; position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
            .main-content {
                width: 100%; padding: 15px; box-sizing: border-box;
                padding-top: 80px;
            }
            .mobile-header {
                display: flex; align-items: center; justify-content: space-between;
                padding: 10px 15px; background-color: #1e293b; color: white;
                position: fixed; top: 0; left: 0; right: 0; z-index: 500;
            }
            .hamburger-btn { background: none; border: none; color: white; font-size: 24px; cursor: pointer; }
            .mobile-header-title { font-size: 18px; font-weight: bold; }
            .content-wrapper { overflow-x: auto; }
            /* Dentro de @media (max-width: 768px) */

            .form-grid, .content-panels { grid-template-columns: 1fr; }
            .stats-cards { grid-template-columns: 1fr 1fr; }
            .content-wrapper { overflow-x: auto; }
            .product-details-grid { grid-template-columns: 1fr; } /* <-- AÑADE ESTA LÍNEA */

        }
    </style>
</head>

<body x-data="{ sidebarOpen: false }">
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="sidebar-overlay" x-cloak></div>

    <div class="dashboard-container">
        <aside class="sidebar" :class="{ 'is-open': sidebarOpen }">
            <div class="sidebar-header">
                <img src="{{ asset('images/logo.png') }}" alt="Accesorios Ramirez" class="app-logo">
            </div>
            <div class="sidebar-user-panel">
                <div class="user-info">
                    <span class="user-name">Hola, {{ Auth::user()->name }}</span>
                    <span class="user-role">{{ ucfirst(Auth::user()->role) }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-link">Salir</button>
                </form>
            </div>

            <ul class="sidebar-nav">
                @if(in_array(auth()->user()->role, ['admin', 'employee']))
                    <li><a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}"><b>Registrar Venta</b></a></li>
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
                    <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Productos</a></li>
                @endif

                @if (auth()->user()->role === 'admin')
                    <li><a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Categorías</a></li>
                    <li><a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">Marcas</a></li>
                    <li><a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">Proveedores</a></li>
                    <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Usuarios</a></li>
                @endif
            </ul>
        </aside>

        <main class="main-content">
            <header class="mobile-header">
                <button @click="sidebarOpen = true" class="hamburger-btn">&#9776;</button>
                <div class="mobile-header-title">Accesorios Ramirez</div>
                <div></div>
            </header>

            @yield('content')
        </main>
    </div>
</body>
</html>
