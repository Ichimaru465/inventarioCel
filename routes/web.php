<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController; // <-- 1. IMPORTA EL CONTROLADOR
use App\Http\Controllers\Auth\AuthenticatedSessionController; // <-- Puede que necesites importarlo para el logout
use App\Http\Controllers\ProductController; // <-- Asegúrate de importar el controlador de productos
use App\Http\Controllers\CategoryController; // <-- Asegúrate de importar el controlador de categorías
use App\Http\Controllers\SupplierController; // <-- Asegúrate de importar el controlador de proveedores
use App\Http\Controllers\BrandController; // <-- Asegúrate de importar el controlador de marcas
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController; // <-- AÑADE ESTA LÍNEA
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

// ... rutas que ya tenías ...

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors(['email' => 'Las credenciales proporcionadas no son correctas.'])->onlyInput('email');
});


// 2. REEMPLAZA TU RUTA DASHBOARD ANTERIOR CON ESTA
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Esta línea crea todas las rutas necesarias para el CRUD de productos (index, create, store, edit, update, destroy)
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('role:admin,employee')->name('products.index');

    // Aquí puedes añadir más rutas protegidas en el futuro
    Route::middleware('role:admin')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/import', [ProductController::class, 'importForm'])->name('products.import');
        Route::post('/products/import', [ProductController::class, 'importProcess'])->name('products.import.process');
        Route::get('/products/import/template', [ProductController::class, 'downloadImportTemplate'])->name('products.import.template');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        // Puedes añadir las rutas para edit, update y destroy aquí también
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // En routes/web.php dentro del middleware('auth')
        Route::get('/dashboard/sales/print-today', [DashboardController::class, 'printTodaySales'])->name('dashboard.sales.printToday');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('users', UserController::class); // <-- AÑADE ESTA LÍNEA
    });

    Route::post('/products/{product}/sell', [ProductController::class, 'sell'])
        ->middleware('role:admin,employee')->name('products.sell');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/dashboard/sales/print-today', [DashboardController::class, 'printTodaySales'])->name('dashboard.sales.printToday');

    // Rutas para Ventas / Boletas (admin y empleado)
    Route::middleware('role:admin,employee')->group(function () {
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('/sales/{sale}/receipt', [SaleController::class, 'downloadReceipt'])->name('sales.receipt.download');
    });
});

// 3. AÑADE LA RUTA PARA CERRAR SESIÓN
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
