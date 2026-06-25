<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::view('/products', 'admin.placeholder', ['title' => 'Products'])->name('products.index');
    Route::view('/categories', 'admin.placeholder', ['title' => 'Categories'])->name('categories.index');
    Route::view('/brands', 'admin.placeholder', ['title' => 'Brands'])->name('brands.index');
    Route::view('/units', 'admin.placeholder', ['title' => 'Units'])->name('units.index');
});
