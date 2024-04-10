<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Management;
use App\Utils\Notify;
use Illuminate\Support\Facades\Route;

Route::post('/auth', [Auth::class, 'auth']);
Route::post('/auth/verify', [Auth::class,'verify']);
Route::post('/auth/recover', [Auth::class,'recover']);
Route::post('/auth/renew', [Auth::class,'renew']);
Route::get('/auth/check', [Auth::class,'check']);

Route::post('/management', [Management::class,'list']);
Route::post('/management/save', [Management::class,'save']);
Route::put('/management/update', [Management::class,'update']);
Route::delete('/management/destroy', [Management::class,'delete']);
Route::get('/management/details', [Management::class,'details']);
Route::get('/management/selects', [Management::class,'selects']);

Route::fallback(function () {  
    return Response()->json(Notify::warning('Destino solicitado não existe...'), 404);
});
