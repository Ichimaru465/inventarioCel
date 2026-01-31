<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Product;

// Esta ruta devolverá los atributos de una categoría específica
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- AÑADE NUESTRA RUTA AQUÍ ---
Route::get('/categories/{category}/attributes', function (Category $category) {
    // La función with() carga la relación para asegurar que los datos vienen
    $category->load('attributes');
    return response()->json($category->attributes);
});

Route::get('/products/search', function (Request $request) {
    $searchTerm = $request->input('q');

    if (!$searchTerm) {
        return response()->json([]);
    }

    $products = Product::with('category')
                       ->where(function($q) use ($searchTerm) {
                           $q->where('name', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('sku', 'LIKE', "%{$searchTerm}%");
                       })
                       ->where('quantity', '>', 0) // Solo mostrar productos con stock
                       ->take(10)
                       ->get();

    return response()->json($products);
});
