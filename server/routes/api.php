<?php

use App\Http\Controllers\Auth;
use App\Utils\Notify;
use Illuminate\Support\Facades\Route;

Route::post('/auth', [Auth::class, 'auth']);
Route::post('/auth/verify', [Auth::class,'verify']);
Route::post('/auth/recover', [Auth::class,'recover']);
Route::post('/auth/renew', [Auth::class,'renew']);
Route::get('/auth/check', [Auth::class,'check']);

Route::fallback(function () {  
    return Response()->json(Notify::warning('Destino solicitado não existe...'),404);
});
