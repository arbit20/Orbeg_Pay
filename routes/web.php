<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PriceCalculatorController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MetalController;
use App\Http\Controllers\MetalPriceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsernameController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Username utilities (accesibles para registro y usuarios autenticados)
Route::post('username/check', [UsernameController::class, 'check'])->name('username.check');
Route::post('username/suggest', [UsernameController::class, 'suggest'])->name('username.suggest');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Modulo Proveedores
    Route::resource('suppliers', SupplierController::class);

    // Modulo Clientes
    Route::resource('clients', ClientController::class);

    // Modulo Metales
    Route::resource('metals', MetalController::class);
    Route::get('metals/{metal}/prices/create', [MetalPriceController::class, 'create'])->name('metals.prices.create');
    Route::post('metals/{metal}/prices', [MetalPriceController::class, 'store'])->name('metals.prices.store');

    // Modulo Compras
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'destroy']);

    // Modulo Ventas
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'destroy']);

    // Solicitudes de compra (clientes y operadores)
    Route::resource('purchase-requests', PurchaseRequestController::class)
        ->only(['index', 'create', 'store', 'show']);

    // Cotizador de precios
    Route::get('price-calculator', PriceCalculatorController::class)
        ->name('price-calculator');

    // Modulo Usuarios (solo administradores)
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
