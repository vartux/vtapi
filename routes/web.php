<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/tokens/create', function () {
    $token = Auth::user()->createToken("firsttoken");
    return ['token' => $token->plainTextToken];
})->middleware("auth");
