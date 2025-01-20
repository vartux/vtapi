<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix("ws")->group(function () {
        Route::controller(ApiController::class)->group(function () {
            Route::post('/send', 'sendSms');
        });
    });
});