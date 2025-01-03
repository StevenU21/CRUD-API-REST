<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::get('/autocomplete', [ProductController::class, 'autocomplete'])->name('autocomplete');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::patch('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
});
