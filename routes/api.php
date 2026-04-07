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

    $limit = (int) $request->input('limit', 200);
    $limit = max(1, min(200, $limit));

    // Búsqueda por nombre, SKU y atributos (JSON como texto) - y prioriza coincidencias al inicio.
    $products = Product::with('category')
        ->where('quantity', '>', 0) // Solo mostrar productos con stock
        ->where(function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
                ->orWhereRaw('LOWER(CAST(attributes AS CHAR)) LIKE ?', ['%' . mb_strtolower($searchTerm) . '%']);
        })
        ->orderByRaw('CASE WHEN name LIKE ? THEN 0 WHEN sku LIKE ? THEN 1 ELSE 2 END', [
            $searchTerm . '%',
            $searchTerm . '%',
        ])
        ->orderBy('name')
        ->limit($limit)
        ->get();

    return response()->json($products);
});
