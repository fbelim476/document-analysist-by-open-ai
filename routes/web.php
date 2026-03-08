<?php

use Illuminate\Support\Facades\Route;
//Default API endpoint
Route::get('/', function () {
    //return ['Laravel' => app()->version()];
    return "";
});
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/dashboard', function () {
    return view('documents.dashboard');
});

require __DIR__.'/auth.php';
