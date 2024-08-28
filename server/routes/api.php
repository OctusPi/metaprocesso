<?php

use App\Http\Controllers\CatalogItems;
use App\Http\Controllers\Catalogs;
use App\Http\Controllers\CatalogSubCategories;
use App\Http\Controllers\ComissionMembers;
use App\Http\Controllers\Comissions;
use App\Http\Controllers\ComissionsEnds;
use App\Http\Controllers\Demandants;
use App\Http\Controllers\Dotations;
use App\Http\Controllers\Home;
use App\Http\Controllers\Ordinators;
use App\Http\Controllers\Processes;
use App\Http\Controllers\Programs;
use App\Http\Controllers\Sectors;
use App\Http\Controllers\Suppliers;
use App\Http\Controllers\Units;
use App\Http\Controllers\Users;
use App\Http\Controllers\Organs;
use App\Http\Controllers\Dfds;
use Illuminate\Support\Facades\Route;
use App\Utils\Notify;
use App\Http\Controllers\Authentication;


// open
Route::prefix('/auth')->controller(Authentication::class)->group(function () {
    Route::post('', 'login');
    Route::get('/check', 'check');
    Route::post('/recover', 'recover');
});


//authenticaded
Route::middleware('auth:sanctum')->group(function () {

    $common = function (string $prefix, string $controller) {
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
    };

    // commons
    $common('/home', Home::class);
    $common('/organs', Organs::class);
    $common('/units', Units::class);
    $common('/sectors', Sectors::class);
    $common('/users', Users::class);
    $common('/ordinators', Ordinators::class);
    $common('/demandants', Demandants::class);
    $common('/comissions', Comissions::class);
    $common('/endedcomissions', ComissionsEnds::class);
    $common('/comissionmembers/{comission}', ComissionMembers::class);
    $common('/programs', Programs::class);
    $common('/dotations', Dotations::class);
    $common('/catalogs', Catalogs::class);
    $common('/catalogitems/{catalog}', CatalogItems::class);
    $common('/suppliers', Suppliers::class);
    $common('/dfds', Dfds::class);
    $common('/processes', Processes::class);

    //especializeds
    Route::prefix('/auth')->controller(Authentication::class)->group(function () {
        Route::post('/renew', 'renew');
        Route::post('/auth_organ', 'auth_organ');
    });

    Route::prefix('/dfds')->controller(Dfds::class)->group(function(){
        Route::post('/items', 'items');
        Route::post('/generate', 'generate');
    });

    Route::prefix('/processes')->controller(Processes::class)->group(function(){
        Route::post('/list_dfds', 'list_dfds');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
    });

    Route::prefix('/catalogitems/{catalog}')->controller(CatalogItems::class)->group(function(){
        Route::get('/catalog', 'catalog');
    });

    Route::prefix('/catalogsubcategories')->controller(CatalogSubCategories::class)->group(function(){
        Route::post('/{organ}/list', 'list');
        Route::post('/{organ}/save', 'save');
        Route::post('/{organ}/fastdestroy', 'fast_delete');
        Route::get('/{organ}/details/{id}', 'details');
    });
});


// fallback 404
Route::fallback(function () {
    return Response()->json(Notify::warning('Destino solicitado não existe...'), 404);
});
