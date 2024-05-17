<?php

use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', function () {
    $user = Auth::user();

    return view('welcome', [
        'data' => $user
    ]);
});



// Google Login
// Route::get('/oauth/register', [GoogleController::class, 'redirect']);

Route::get('/logout', function () {
    Auth::logout();

     return redirect('/home');
});
