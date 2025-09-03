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
        'discount_percent' => 'nullable|numeric|min:0|max:100',
        'discount_fixed' => 'nullable|numeric|min:0',
    ]);

    $cartItems = $request->input('products');
    $productsFromDB = Product::find(collect($cartItems)->pluck('product_id'));

    // Calcular subtotal
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $product = $productsFromDB->find($item['product_id']);
        $subtotal += $product->price * $item['quantity'];
    }

    // Calcular descuento total
    $totalDiscount = 0;
    if ($request->filled('discount_percent') && $request->discount_percent > 0) {
        $totalDiscount = ($subtotal * $request->discount_percent) / 100;
    } else if ($request->filled('discount_fixed')) {
        $totalDiscount = $request->discount_fixed;
    }
    // Asegurar que el descuento no sea mayor al subtotal
    $totalDiscount = min($subtotal, $totalDiscount);

    try {
        DB::transaction(function () use ($cartItems, $productsFromDB, $subtotal, $totalDiscount) {
            foreach ($cartItems as $item) {
                $product = $productsFromDB->find($item['product_id']);
                $quantityToSell = $item['quantity'];

                if ($product->quantity < $quantityToSell) {
                    throw ValidationException::withMessages(['products' => "Stock insuficiente para: {$product->name}."]);
                }

                // Calcular descuento proporcional para este item
                $itemSubtotal = $product->price * $quantityToSell;
                $proportionalDiscount = ($subtotal > 0) ? ($itemSubtotal / $subtotal) * $totalDiscount : 0;

                // Descontar stock
                $product->decrement('quantity', $quantityToSell);

                // Registrar movimiento con precio y descuento
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'salida',
                    'quantity' => $quantityToSell,
                    'price' => $product->price, // Precio de venta unitario original
                    'discount_amount' => $proportionalDiscount, // Descuento total para esta línea
                    'reason' => 'Venta',
                ]);
            }
        });
    } catch (ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    return redirect()->route('products.index')->with('success', '¡Venta registrada exitosamente!');
}
}
