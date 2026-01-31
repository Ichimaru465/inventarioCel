<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Lista de ventas (boletas) para re-descarga.
     */
    public function index()
    {
        $sales = Sale::with(['user'])
            ->latest()
            ->paginate(20);

        return view('sales.index', compact('sales'));
    }

    /**
     * Muestra la vista para crear una nueva venta.
     */
    public function create()
    {
        return view('sales.create');
    }

    /**
     * Muestra el detalle de una venta (y dispara descarga si se solicita).
     */
    public function show(Sale $sale, Request $request)
    {
        $sale->load(['items', 'user']);

        $autoDownload = (bool) $request->boolean('download');
        return view('sales.show', compact('sale', 'autoDownload'));
    }

    /**
     * Descarga (y guarda si falta) la boleta PDF.
     */
    public function downloadReceipt(Sale $sale)
    {
        $sale->load(['items', 'user']);

        $path = $this->normalizeReceiptPath($sale->receipt_path);

        // Si no existe, intenta generarla de nuevo y refresca el modelo.
        if (!$path || !Storage::disk('local')->exists($path)) {
            $this->generateAndStoreReceiptPdf($sale);
            $sale->refresh();
            $path = $this->normalizeReceiptPath($sale->receipt_path);
        }

        // Compatibilidad con boletas guardadas con el prefijo "private/" (bug anterior)
        if ($path && !Storage::disk('local')->exists($path) && $sale->receipt_path && Storage::disk('local')->exists($sale->receipt_path)) {
            $path = $sale->receipt_path;
        }

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(500, 'No se pudo generar/ubicar la boleta PDF. Verifica permisos de escritura en storage/app/private.');
        }

        $absolutePath = Storage::disk('local')->path($path);
        $filename = ($sale->receipt_number ?: ('BOLETA-' . $sale->id)) . '.pdf';

        return response()->download($absolutePath, $filename);
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
    $productsFromDB = Product::with('category')->find(collect($cartItems)->pluck('product_id'));

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
        $saleId = null;

        DB::transaction(function () use ($cartItems, $productsFromDB, $subtotal, $totalDiscount, &$saleId) {
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'discount_total' => $totalDiscount,
                'total' => max(0, $subtotal - $totalDiscount),
            ]);

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
                    'sale_id' => $sale->id,
                    'type' => 'salida',
                    'quantity' => $quantityToSell,
                    'price' => $product->price, // Precio de venta unitario original
                    'discount_amount' => $proportionalDiscount, // Descuento total para esta línea
                    'reason' => 'Venta',
                ]);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_category_name' => $product->category?->name,
                    'product_attributes' => $product->attributes ?? [],
                    'price' => $product->price,
                    'quantity' => $quantityToSell,
                    'discount_amount' => $proportionalDiscount,
                ]);
            }

            $sale->receipt_number = 'BOL-' . now()->format('Ymd') . '-' . str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT);
            $sale->save();

            $saleId = $sale->id;
        });
    } catch (ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    $sale = Sale::findOrFail($saleId);
    $this->generateAndStoreReceiptPdf($sale);

    // Página de confirmación que auto-descarga la boleta
    return redirect()
        ->route('sales.show', ['sale' => $sale->id, 'download' => 1])
        ->with('success', '¡Venta registrada exitosamente! Se generó la boleta.');
}

    private function generateAndStoreReceiptPdf(Sale $sale): void
    {
        $sale->loadMissing(['items', 'user']);

        $pdf = Pdf::loadView('sales.receipt_pdf', [
            'sale' => $sale,
        ])->setPaper('a4');

        // OJO: en Laravel 12 el disco "local" apunta a storage/app/private
        Storage::disk('local')->makeDirectory('boletas');

        $relativePath = 'boletas/boleta-' . $sale->id . '.pdf';
        $written = Storage::disk('local')->put($relativePath, $pdf->output());

        if (!$written) {
            abort(500, 'No se pudo guardar la boleta PDF en disco. Revisa permisos de escritura en storage/app.');
        }

        $sale->receipt_path = $relativePath;
        $sale->save();
    }

    private function normalizeReceiptPath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Bug anterior: guardábamos con "private/..." aunque el disco ya es "private"
        if (str_starts_with($path, 'private/')) {
            return substr($path, strlen('private/'));
        }

        return $path;
    }
}
