<?php

use App\Http\Controllers\Ordinators;
use App\Http\Controllers\Sectors;
use App\Http\Controllers\Units;
use App\Http\Controllers\Users;
use App\Utils\Notify;
use App\Http\Controllers\Organs;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication;

function common(string $prefix, string $controller)
{
    Route::prefix($prefix)->controller($controller)->group(function () {
        Route::post('/save', 'save');
        Route::post('/destroy', 'delete');
        Route::post('/list', 'list');
        Route::get('/details/{id}', 'details');
        Route::get('/selects/{key?}/{search?}', 'selects');
        Route::get('/download/{id}', 'download');
        Route::get('/export/{id}', 'export');
        Route::get('', 'index');
    });
}


// open
Route::prefix('/auth')->controller(Authentication::class)->group(function () {
    Route::post('', 'login');
    Route::post('/recover', 'recover');
});


//authenticaded
Route::middleware('auth:sanctum')->group(function () {

    // commons
    common('/organs', Organs::class);
    common('/units', Units::class);
    common('/sectors', Sectors::class);
    common('/users', Users::class);
    common('/ordinators', Ordinators::class);

    //especializeds
    Route::prefix('/auth')->controller(Authentication::class)->group(function () {
        Route::post('/renew', 'renew');
        Route::get('/check', 'check');
        Route::post('/auth_organ', 'auth_organ');
    });
});


// fallback 404
Route::fallback(function () {
    return Response()->json(Notify::warning('Destino solicitado não existe...'), 404);
});
