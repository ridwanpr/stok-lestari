<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\Cron\ExpireBatchesWebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlashSaleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::match(['get', 'post'], '/cron/expire-batches', ExpireBatchesWebhookController::class)
    ->middleware('throttle:10,1')
    ->name('cron.expire-batches');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('products', ProductController::class)->only([
        'index', 'create', 'store', 'edit', 'update', 'destroy',
    ]);

    Route::resource('products.batches', BatchController::class)->shallow()->only([
        'store', 'edit', 'update', 'destroy',
    ]);

    Route::get('/flash-sales', [FlashSaleController::class, 'index'])->name('flash-sales.index');
    Route::post('/flash-sales', [FlashSaleController::class, 'store'])->name('flash-sales.store');
    Route::patch('/flash-sales/{flashSale}/toggle', [FlashSaleController::class, 'toggle'])->name('flash-sales.toggle');
    Route::delete('/flash-sales/{flashSale}', [FlashSaleController::class, 'destroy'])->name('flash-sales.destroy');
});
