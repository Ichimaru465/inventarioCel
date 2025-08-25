<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    /**
     * Muestra la vista para crear una nueva venta.
     */
    public function create()
    {
        return view('sales.create');
    }

    /**
     * Almacena una nueva venta en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos de entrada
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // 2. Usar una transacción para asegurar la integridad de los datos
            DB::transaction(function () use ($request) {
                foreach ($request->input('products') as $item) {
                    $product = Product::find($item['product_id']);
                    $quantityToSell = $item['quantity'];

                    // Verificar si hay suficiente stock
                    if ($product->quantity < $quantityToSell) {
                        // Lanzar una excepción para cancelar la transacción
                        throw ValidationException::withMessages([
                            'products' => "No hay suficiente stock para el producto: {$product->name}. Disponible: {$product->quantity}.",
                        ]);
                    }

                    // Descontar el stock
                    $product->decrement('quantity', $quantityToSell);

                    // Registrar el movimiento
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                        'type' => 'salida',
                        'quantity' => $quantityToSell,
                        'reason' => 'Venta',
                    ]);
                }
            });
        } catch (ValidationException $e) {
            // Si la validación de stock falla, regresar con el error
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // 3. Redirigir con mensaje de éxito
        return redirect()->route('products.index')->with('success', '¡Venta registrada exitosamente!');
    }
}
