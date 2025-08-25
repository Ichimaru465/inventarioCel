<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryMovement;

class DashboardController extends Controller
{
    public function index()
{
    // --- MÉTRICAS GENERALES ---
    $totalProducts = Product::count();
    $totalItemsInStock = Product::sum('quantity');
    $inventoryValue = Product::select(DB::raw('SUM(cost * quantity) as total_value'))->first()->total_value;
    $totalCategories = Category::count();

    // --- PANELES ORIGINALES DEL DASHBOARD ---
    $lowStockProducts = Product::where('quantity', '<=', 10)->orderBy('quantity', 'asc')->take(5)->get();
    $recentlyAddedProducts = Product::latest()->take(5)->get();

    // --- MÉTRICAS DE VENTAS DEL DÍA ---
    $salesToday = InventoryMovement::where('type', 'salida')
                                   ->whereDate('created_at', today())
                                   ->with('product')
                                   ->get();

    $totalSalesTodayCount = $salesToday->count();
    $totalItemsSoldToday = $salesToday->sum('quantity');
    $totalRevenueToday = $salesToday->sum(fn($movement) => $movement->quantity * ($movement->product->price ?? 0));
    $latestSalesToday = $salesToday->sortByDesc('created_at')->take(5);

    // --- ENVIAR TODOS LOS DATOS A LA VISTA ---
    return view('dashboard', compact(
        'totalProducts',
        'totalItemsInStock',
        'inventoryValue',
        'totalCategories',
        'lowStockProducts', // <-- Asegurarse de que esté aquí
        'recentlyAddedProducts', // <-- Y aquí
        'totalSalesTodayCount',
        'totalItemsSoldToday',
        'totalRevenueToday',
        'latestSalesToday'
    ));
}

    // Nueva función para imprimir las ventas del día
    public function printTodaySales()
    {
        // 1. Obtener todas las ventas de hoy
        $salesToday = InventoryMovement::where('type', 'salida')
                                       ->whereDate('created_at', today())
                                       ->with('product', 'user')
                                       ->latest()
                                       ->get();

        // 2. Calcular los totales para el pie de página del reporte
        $totalItems = $salesToday->sum('quantity');
        $totalRevenue = $salesToday->sum(fn($sale) => $sale->quantity * ($sale->product->price ?? 0));

        // 3. Devolver una nueva vista de impresión
        return view('dashboard.print_sales_today', compact('salesToday', 'totalItems', 'totalRevenue'));
    }

}
