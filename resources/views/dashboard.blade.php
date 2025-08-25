<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- Estilos Generales --- */
        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        .dashboard-container {
            display: flex;
        }

        /* --- Barra Lateral (Sidebar) --- */
        .sidebar {
            width: 250px;
            background-color: #1e293b;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 1px solid #334155;
        }
        .sidebar-nav {
            list-style: none;
            padding: 20px 0;
            margin: 0;
            flex-grow: 1;
        }
        .sidebar-nav a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 15px 20px;
            transition: background-color 0.3s, color 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: #334155;
            color: white;
        }
        .logout-form {
            padding: 20px;
        }
        .logout-button {
            width: 100%;
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .logout-button:hover {
            background-color: #dc2626;
        }


        /* --- Contenido Principal --- */
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .main-header h1 {
            margin: 0;
        }
        .user-info {
            font-weight: 600;
        }

        /* --- Tarjetas de Estadísticas --- */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .card h3 {
            margin-top: 0;
            font-size: 16px;
            color: #64748b;
        }
        .card .value {
            font-size: 32px;
            font-weight: bold;
        }

        /* --- Paneles de Contenido --- */
        .content-panels {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .panel {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .panel h2 {
            margin-top: 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .panel-table {
            width: 100%;
            border-collapse: collapse;
        }
        .panel-table th, .panel-table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .panel-table th { font-weight: 600; }
        .low-stock { color: #ef4444; font-weight: bold; }
        .no-data { color: #64748b; }
    </style>
</head>
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <header class="main-header">
        <h1>Dashboard</h1>
        <div class="user-info">
            Hola, {{ Auth::user()->name }}
        </div>
    </header>

    <section class="stats-cards">
        <div class="card">
            <h3>Productos Totales</h3>
            <p class="value">{{ $totalProducts }}</p>
        </div>
        <div class="card">
            <h3>Items en Stock</h3>
            <p class="value">{{ $totalItemsInStock }}</p>
        </div>
        <div class="card">
            <h3>Valor del Inventario</h3>
            <p class="value">S/ {{ number_format($inventoryValue, 2) }}</p>
        </div>
        <div class="card">
            <h3>Categorías</h3>
            <p class="value">{{ $totalCategories }}</p>
        </div>
    </section>

    <section class="content-panels">
        <div class="panel">
            <h2>Productos con Bajo Stock (<= 10)</h2>
            <table class="panel-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lowStockProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td class="low-stock">{{ $product->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="no-data">No hay productos con bajo stock.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h2>Productos Recientes</h2>
             <table class="panel-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Añadido</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentlyAddedProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="no-data">No se han añadido productos recientemente.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Tarjetas con el Resumen del Día --}}
    <h2 style="font-weight: 600; margin-top: 30px; margin-bottom: 15px;">Resumen de Hoy ({{ now()->format('d/m/Y') }})</h2>
    <section class="stats-cards">
        <div class="card">
            <h3>Ingresos del Día</h3>
            <p class="value">S/ {{ number_format($totalRevenueToday, 2) }}</p>
        </div>
        <div class="card">
            <h3>Nº de Ventas</h3>
            <p class="value">{{ $totalSalesTodayCount }}</p>
        </div>
        <div class="card">
            <h3>Items Vendidos</h3>
            <p class="value">{{ $totalItemsSoldToday }}</p>
        </div>
    </section>

    {{-- Panel de Ventas Recientes del Día --}}
    <section class="panel full-width-panel" style="margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Ventas Recientes del Día</h2>
            <a href="{{ route('dashboard.sales.printToday') }}" target="_blank" class="btn btn-secondary">Imprimir Reporte del Día</a>
        </div>
        <table class="panel-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Vendido por</th>
                    <th>Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($latestSalesToday as $sale)
                    <tr>
                        <td>{{ $sale->product->name ?? 'Producto Eliminado' }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>S/ {{ number_format($sale->quantity * ($sale->product->price ?? 0), 2) }}</td>
                        <td>{{ $sale->user->name ?? 'N/A' }}</td>
                        <td>{{ $sale->created_at->format('h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="no-data">Aún no se han registrado ventas hoy.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</section>

{{-- Pequeños estilos para el nuevo panel. Puedes moverlos a tu CSS principal en app.blade.php --}}
<style>
    .full-width-panel { grid-column: 1 / -1; }
    .status-entrada { background-color: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: 600; }
    .status-salida { background-color: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: 600; }
    .status-ajuste { background-color: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: 600; }
</style>
@endsection
