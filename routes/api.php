<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        "API" => "Uts Pemograman Berbasis Framework",
        "nama" => "Firmansyah Maulana",
        "nim" => "22416255201228",
    ], 200);
});


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);


// login google
Route::get('/oauth/google/redirect', [GoogleController::class, 'redirect']);
Route::get('/oauth/google/callback', [GoogleController::class, 'callback']);
Route::get('/oauth/register', [GoogleController::class, 'register']);


// product list
Route::group(['middleware' => ['auth:api']], function () {
    Route::get("/products", [ProductController::class, 'index']);
    Route::post("/products", [ProductController::class, 'store']);
    Route::put("/products/{id}", [ProductController::class, 'update']);
    Route::delete("/products/{id}", [ProductController::class, 'destroy']);
});



// Category list
Route::group(['middleware' => 'auth:api', "admin"], function () {
    Route::get("/categories", [CategoryController::class, 'index']);
    Route::post("/categories", [CategoryController::class, 'store']);
    Route::put("/categories/{id}", [CategoryController::class, 'update']);
    Route::delete("/categories/{id}", [CategoryController::class, 'destroy']);
});
