<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
});