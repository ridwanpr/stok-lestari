<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

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

    Route::get('/flash-sales', function () {
        return view('placeholder', ['title' => 'Flash Sale']);
    })->name('flash-sales.index');
});
