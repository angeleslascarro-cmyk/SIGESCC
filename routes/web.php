<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\NeighborController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController; 
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth','verified'])->group(function () {

        Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::middleware('role:admin,agent')->group(function () {
        Route::resource('products', ProductController::class)->only(['index','create','store']);
        Route::resource('neighbors', NeighborController::class)->only(['index','create','store']);

        Route::get('orders', [OrderController::class,'index'])->name('orders.index');
        Route::get('orders/create', [OrderController::class,'create'])->name('orders.create');
        Route::post('orders', [OrderController::class,'store'])->name('orders.store');

        Route::get('reports', [ReportController::class,'index'])->name('reports.index');
        Route::get('reports/debtors.csv', [ReportController::class,'debtorsCsv'])->name('reports.debtors.csv');
        Route::get('reports/monthly.csv', [ReportController::class,'monthlyCsv'])->name('reports.monthly.csv');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('products', ProductController::class)->only(['edit','update','destroy']);
        Route::resource('neighbors', NeighborController::class)->only(['edit','update','destroy']);
    });
});

require __DIR__.'/auth.php';
