<?php

use App\Http\Controllers\Attachments;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\CatalogItems;
use App\Http\Controllers\Catalogs;
use App\Http\Controllers\CatalogSubCategoryItems;
use App\Http\Controllers\Comissions;
use App\Http\Controllers\ComissionsEnds;
use App\Http\Controllers\ComissionsMembers;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Demandants;
use App\Http\Controllers\Dfds;
use App\Http\Controllers\Dotations;
use App\Http\Controllers\Etps;
use App\Http\Controllers\Management;
use App\Http\Controllers\Ordinators;
use App\Http\Controllers\Organs;
use App\Http\Controllers\PriceRecords;
use App\Http\Controllers\Processes;
use App\Http\Controllers\Programs;
use App\Http\Controllers\RiskMaps;
use App\Http\Controllers\Suppliers;
use App\Http\Controllers\Units;
use App\Http\Controllers\Sectors;
use App\Utils\Notify;
use Illuminate\Support\Facades\Route;

function common(string $prefix, string $controller){
    Route::prefix($prefix)->controller($controller)->group(function(){
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

// open routers
Route::prefix('auth')->controller(Authentication::class)->group(function(){
    Route::post('', 'login');
    Route::post('/recover', 'recover');
    Route::post('/renew', 'renew');
    Route::get('/check', 'check');
});

// protected routes
Route::middleware('auth:sanctum')->group(function(){
    
    // call common routes
    common('/home', Dashboard::class);
    common('/dashboard', Dashboard::class);
    common('/organs', Organs::class);
    common('/units', Units::class);
    common('/sectors', Sectors::class);
    common('/ordinators', Ordinators::class);
    common('/demandants', Demandants::class);
    common('/comissions', Comissions::class);
    common('/comissionsmembers', ComissionsMembers::class);
    common('/comissionsends', ComissionsEnds::class);
    common('/programs', Programs::class);
    common('/dotations', Dotations::class);
    common('/users', Management::class);
    common('/catalogs', Catalogs::class);
    common('/suppliers', Suppliers::class);
    common('/dfds', Dfds::class);
    common('/processes', Processes::class);
    common('/etps', Etps::class);
    common('/pricerecords', PriceRecords::class);
    common('/riskiness', RiskMaps::class);


    // call especialized routes
    Route::prefix('/dashboard')->controller(Dashboard::class)->group(function(){
        Route::get('', 'index');
    });

    Route::prefix('/catalogitems')->controller(CatalogItems::class)->group(function(){
        Route::get('/{catalog}', 'index');
        Route::post('/{catalog}/list', 'list');
        Route::post('/{catalog}/save', 'save');
        Route::post('/{catalog}/destroy', 'delete');
        Route::get('/{catalog}/details/{id}', 'details');
        Route::get('/{catalog}/catalog', 'catalog');
        Route::get('/{catalog}/selects/{key?}/{search?}', 'selects');
    });

    Route::prefix('/catalogsubcategories')->controller(CatalogSubCategoryItems::class)->group(function(){
        Route::post('/{organ}/list', 'list');
        Route::post('/{organ}/save', 'save');
        Route::post('/{organ}/fastdestroy', 'fastdestroy');
        Route::get('/{organ}/details/{id}', 'details');
    });

    Route::prefix('/dfds')->controller(Dfds::class)->group(function(){
        Route::post('/generate', 'generate');
        Route::post('/items', 'items');
    });

    Route::prefix('/process')->controller(Processes::class)->group(function(){
        Route::post('/list_dfds', 'list_dfds');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
    });

    Route::prefix('/etps')->controller(Etps::class)->group(function(){
        Route::post('/generate', 'generate');
        Route::post('/list_processes', 'list_processes');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
    });

    Route::prefix('/pricerecords')->controller(PriceRecords::class)->group(function(){
        Route::post('/list_processes', 'list_processes');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
        Route::post('/list_suppliers', 'list_suppliers');
    });

    Route::prefix('/attachments')->controller(Attachments::class)->group(function(){
        Route::get('', 'index');
        Route::post('/{origin}/{protocol}/list', 'list');
        Route::post('/{origin}/{protocol}/save', 'save');
        Route::post('/{origin}/{protocol}/destroy', 'delete');
        Route::get('/{origin}/{protocol}/details/{id}', 'details');
        Route::get('/{origin}/{protocol}/download/{id}', 'download');
    });

    Route::prefix('/riskiness')->controller(Processes::class)->group(function(){
        Route::post('/list_processes', 'list_processes');
    });
});

// fallback 404
Route::fallback(function () {
    return Response()->json(Notify::warning('Destino solicitado não existe...'), 404);
});