<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication;

Route::prefix('/auth')->controller(Authentication::class)->group(function(){
    Route::post('', 'login');
    Route::post('/recover', 'recover');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/auth')->controller(Authentication::class)->group(function(){
        Route::post('/renew', 'renew');
        Route::get('/check', 'check');
        Route::post('/auth_organ', 'auth_organ');
    });
});
