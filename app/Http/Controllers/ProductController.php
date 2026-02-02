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
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'sku' => 'nullable|string',
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
        'sku' => 'nullable|string',
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

    /**
     * Muestra el formulario para importar productos desde CSV.
     */
    public function importForm()
    {
        return view('products.import');
    }

    /**
     * Procesa el CSV subido y crea/actualiza productos.
     */
    public function importProcess(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv');
        $rows = $this->parseCsv($file);
        if (empty($rows)) {
            return redirect()->route('products.import')->with('error', 'El archivo CSV está vacío o no tiene filas válidas.');
        }

        $firstRow = $rows[0];
        if (isset($firstRow[0])) {
            $firstRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', $firstRow[0]); // quitar BOM UTF-8
        }
        $headers = array_map('trim', array_map('strtolower', $firstRow));
        $created = 0;
        $errors = [];

        foreach (array_slice($rows, 1) as $index => $row) {
            $line = $index + 2; // 1-based + header
            $row = array_pad($row, count($headers), null);
            $data = array_combine($headers, $row);
            if ($data === false) {
                continue;
            }

            $name = trim($data['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $categoryId = $this->resolveCategoryId($data);
            if ($categoryId === null) {
                $errors[] = "Línea {$line}: categoría no encontrada (usa category_id o category).";
                continue;
            }

            $price = $this->parseDecimal($data['price'] ?? 0) ?? 0;
            $cost = $this->parseDecimal($data['cost'] ?? null);
            $quantity = (int) ($data['quantity'] ?? 0);
            if ($quantity < 0) {
                $quantity = 0;
            }

            $attributes = [];
            if (!empty($data['attributes'])) {
                $decoded = json_decode(trim($data['attributes']), true);
                if (is_array($decoded)) {
                    $attributes = $decoded;
                }
            }

            try {
                Product::create([
                    'name' => $name,
                    'sku' => trim($data['sku'] ?? '') ?: null,
                    'description' => trim($data['description'] ?? '') ?: null,
                    'price' => $price,
                    'cost' => $cost,
                    'quantity' => $quantity,
                    'category_id' => $categoryId,
                    'brand_id' => $this->resolveBrandId($data),
                    'supplier_id' => $this->resolveSupplierId($data),
                    'attributes' => $attributes,
                ]);
                $created++;
            } catch (\Throwable $e) {
                $errors[] = "Línea {$line}: " . $e->getMessage();
            }
        }

        if ($created === 0 && !empty($errors)) {
            $message = 'No se importó ningún producto. ' . implode(' ', array_slice($errors, 0, 3));
            if (count($errors) > 3) {
                $message .= ' ... (y ' . (count($errors) - 3) . ' más)';
            }
            return redirect()->route('products.import')->with('error', $message);
        }

        $message = "Se importaron {$created} producto(s).";
        if (!empty($errors)) {
            $message .= ' Algunas filas tuvieron errores.';
        }
        return redirect()->route('products.index')->with('success', $message);
    }

    /**
     * Descarga una plantilla CSV de ejemplo.
     */
    public function downloadImportTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ];

        $callback = function () {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
            fputcsv($out, [
                'name', 'sku', 'description', 'price', 'cost', 'quantity',
                'category_id', 'brand_id', 'supplier_id', 'attributes',
            ]);
            // Ejemplo
            fputcsv($out, [
                'Producto ejemplo', 'COD-001', 'Descripción', '10.50', '5.00', '20',
                '1', '', '', '{"color":"Rojo","talla":"M"}',
            ]);
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function parseCsv(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }
        return $rows;
    }

    private function parseDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = str_replace(',', '.', trim((string) $value));
        return is_numeric($value) ? (float) $value : null;
    }

    private function resolveCategoryId(array $data): ?int
    {
        if (!empty($data['category_id']) && is_numeric($data['category_id'])) {
            $cat = Category::find((int) $data['category_id']);
            return $cat?->id;
        }
        if (!empty($data['category'])) {
            $name = trim($data['category']);
            $cat = Category::firstOrCreate(['name' => $name], ['name' => $name]);
            return $cat->id;
        }
        return null;
    }

    private function resolveBrandId(array $data): ?int
    {
        if (isset($data['brand_id']) && $data['brand_id'] !== '' && is_numeric($data['brand_id'])) {
            $b = Brand::find((int) $data['brand_id']);
            return $b?->id;
        }
        if (!empty($data['brand'])) {
            $name = trim($data['brand']);
            $b = Brand::firstOrCreate(['name' => $name], ['name' => $name]);
            return $b->id;
        }
        return null;
    }

    private function resolveSupplierId(array $data): ?int
    {
        if (isset($data['supplier_id']) && $data['supplier_id'] !== '' && is_numeric($data['supplier_id'])) {
            $s = Supplier::find((int) $data['supplier_id']);
            return $s?->id;
        }
        if (!empty($data['supplier'])) {
            $name = trim($data['supplier']);
            $s = Supplier::firstOrCreate(['name' => $name], ['name' => $name]);
            return $s->id;
        }
        return null;
    }
}
