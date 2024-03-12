<?php

use App\Http\Controllers\Auth;
use App\Utils\Notify;
use Illuminate\Support\Facades\Route;

Route::post('/auth', [Auth::class, 'auth']);
Route::get('/auth/verify', [Auth::class,'verify']);

Route::fallback(function () {  
    return Response()->json(Notify::warning('Destino solicitado não existe...'),404);
});
