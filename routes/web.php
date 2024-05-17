<?php

use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



// Google Login
// Route::get('/oauth/register', [GoogleController::class, 'redirect']);
Route::get('/oauth/google/redirect', [GoogleController::class, 'redirect']);
Route::get('/oauth/google/callback', [GoogleController::class, 'callback']);