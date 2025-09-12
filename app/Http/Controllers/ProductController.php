<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         // 1. Empezar una consulta base de productos
        $query = Product::with('category')->latest();

        // 2. Si hay un término de búsqueda, filtrar la consulta
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
                  ->orWhere(DB::raw('LOWER(attributes)'), 'LIKE', "%{$searchTerm}%")
                  ->orWhere(DB::raw('LOWER(description)'), 'LIKE', "%{$searchTerm}%");
            });
        }

        // 3. Paginar los resultados (filtrados o no)
        $products = $query->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get(); // <-- ESTA LÍNEA ES LA CLAVE

        // Las pasamos a la vista usando compact()
        return view('products.create', compact('categories', 'brands', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validación de los datos
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'attributes' => 'nullable|array' // Valida que los atributos sean un array
        ]);

        // 2. Crear y guardar el producto
        Product::create($validatedData);

        // 3. Redirigir con un mensaje de éxito
        return redirect()->route('products.index')->with('success', '¡Producto creado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Obtenemos los datos para los dropdowns, igual que en create()
    $categories = Category::orderBy('name')->get();
    $brands = Brand::orderBy('name')->get();
    $suppliers = Supplier::orderBy('name')->get();

    return view('products.edit', compact('product', 'categories', 'brands', 'suppliers'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        // Validación de unicidad que ignora el producto actual
        'sku' => ['nullable', 'string', Rule::unique('products')->ignore($product->id)],
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'cost' => 'nullable|numeric|min:0',
        'quantity' => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
        'brand_id' => 'nullable|exists:brands,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'attributes' => 'nullable|array'
    ]);

    $product->update($validatedData);

    return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');

    }

    public function sell(Product $product)
    {
        // 1. Validar que haya stock
        if ($product->quantity < 1) {
            return back()->with('error', 'No hay stock disponible para este producto.');
        }

        // 2. Descontar el stock
        $product->decrement('quantity'); // Resta 1 a la cantidad

        // 3. (Opcional pero recomendado) Registrar el movimiento
        InventoryMovement::create([
            'product_id' => $product->id,
            'type' => 'salida',
            'quantity' => 1,
            'reason' => 'Venta',
            'user_id' => Auth::id() // Registra qué usuario hizo la venta
        ]);

        return back()->with('success', 'Venta registrada. Stock actualizado.');
    }
}
