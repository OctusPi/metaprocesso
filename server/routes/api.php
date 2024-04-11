<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Management;
use App\Utils\Notify;
use Illuminate\Support\Facades\Route;

Route::controller(Auth::class)->group(function () {
    Route::post('/auth', 'auth');
    Route::post('/auth/verify', 'verify');
    Route::post('/auth/recover', 'recover');
    Route::post('/auth/renew', 'renew');
    Route::get('/auth/check', 'check');
});

Route::controller(Management::class)->group(function () {
    Route::post('/management', 'list');
    Route::post('/management/save', 'save');
    Route::put('/management/update', 'update');
    Route::delete('/management/destroy', 'delete');
    Route::get('/management/details/{id}', 'details');
    Route::get('/management/selects', 'selects');
});

Route::fallback(function () {  
    return Response()->json(Notify::warning('Destino solicitado não existe...'), 404);
});

