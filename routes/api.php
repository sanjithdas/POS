<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;

Route::get('/test', function () {
    $x = "20";
    return response()->json(["hi", $x]);
});

Route::get('/user', function (Request $request) {
    $x = 20;
    $us =  Auth::user();
    return response()->json(Auth::user());
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/orders', [OrderController::class, 'index'])->middleware('can:viewAny,App\Models\Order');
    Route::post('/orders', [OrderController::class, 'store'])->middleware('can:create,App\Models\Order');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('can:view,order');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->middleware('can:update,order');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->middleware('can:delete,order');


    Route::get('/products', [ProductController::class, 'index'])->middleware('can:viewAny,App\Models\Product');
    Route::post('/products', [ProductController::class, 'store'])->middleware('can:create,App\Models\Product');
    Route::get('/products/{product}', [ProductController::class, 'show'])->middleware('can:view,product');
    Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('can:update,product');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('can:delete,product');
});
