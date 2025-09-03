<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;

// --- RUTA PRINCIPAL ---
// Si el usuario visita la raíz, lo redirigimos al login.
Route::get('/', function () {
    return redirect()->route('login');
});

// --- RUTAS DE AUTENTICACIÓN ---
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
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

// --- RUTAS PROTEGIDAS (REQUIEREN INICIO DE SESIÓN) ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // --- RUTAS PARA TODOS LOS USUARIOS LOGUEADOS (ADMIN Y EMPLEADO) ---
    Route::middleware('role:admin,employee')->group(function () {
        // Ventas
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');

        // Productos (acciones comunes)
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::post('/products/{product}/sell', [ProductController::class, 'sell'])->name('products.sell');
    });

    // --- RUTAS EXCLUSIVAS PARA ADMINISTRADORES ---
    Route::middleware('role:admin')->group(function () {
        // Productos (acciones de admin)
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Reporte del Dashboard
        Route::get('/dashboard/sales/print-today', [DashboardController::class, 'printTodaySales'])->name('dashboard.sales.printToday');

        // Gestión completa de otros recursos
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('users', UserController::class);
    });
});
