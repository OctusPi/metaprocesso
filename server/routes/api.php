<?php

use App\Http\Controllers\CatalogItems;
use App\Http\Controllers\Catalogs;
use App\Http\Controllers\CatalogSubCategories;
use App\Http\Controllers\ComissionMembers;
use App\Http\Controllers\Comissions;
use App\Http\Controllers\ComissionsEnds;
use App\Http\Controllers\Demandants;
use App\Http\Controllers\Dotations;
use App\Http\Controllers\Etps;
use App\Http\Controllers\Home;
use App\Http\Controllers\Attachments;
use App\Http\Controllers\Ordinators;
use App\Http\Controllers\PriceRecords;
use App\Http\Controllers\Processes;
use App\Http\Controllers\Programs;
use App\Http\Controllers\Proposals;
use App\Http\Controllers\ProposalsSupplier;
use App\Http\Controllers\RefTerms;
use App\Http\Controllers\RiskMaps;
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
    Route::post('/recover', 'recover');
    Route::get('logout', 'logout');
    Route::get('/check', 'check');
    Route::get('/auth_renew/{token_renew}', 'auth_renew');
    Route::post('/renew', 'renew');
});

Route::prefix('/proposal_supplier')->controller(ProposalsSupplier::class)->group(function(){
    Route::post('/save', 'save');
    Route::post('/details', 'save');
    Route::get('/check', 'check');
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
    $common('/etps', Etps::class);
    $common('/pricerecords', PriceRecords::class);
    $common('/proposals', Proposals::class);
    $common('/attachments/{origin}/{protocol}', Attachments::class);
    $common('/riskiness', RiskMaps::class);
    $common('/refterms', RefTerms::class);

    //especializeds
    Route::prefix('/auth')->controller(Authentication::class)->group(function () {
        Route::post('/auth_organ', 'auth_organ');
    });

    Route::prefix('/dfds')->controller(Dfds::class)->group(function(){
        Route::post('/items', 'items');
        Route::post('/generate', 'generate');
    });

    Route::prefix('/etps')->controller(Etps::class)->group(function(){
        Route::post('/list_processes', 'list_processes');
        Route::post('/list_dfds', 'list_dfds');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
        Route::get('/export/{id}', 'export');
        Route::post('/generate', 'generate');

    });

    Route::prefix('/pricerecords')->controller(PriceRecords::class)->group(function(){
        Route::post('/list_processes', 'list_processes');
        Route::post('/list_suppliers', 'list_suppliers');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
        Route::post('/generate', 'generate');
    });

    Route::prefix('/processes')->controller(Processes::class)->group(function(){
        Route::post('/list_dfds', 'list_dfds');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
        Route::post('/generate', 'generate');
    });

    Route::prefix('/riskiness')->controller(Etps::class)->group(function(){
        Route::post('/list_processes', 'list_processes');
        Route::post('/list_dfds', 'list_dfds');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
    });

    Route::prefix('/refterms')->controller(RefTerms::class)->group(function(){
        Route::post('/list_processes', 'list_processes');
        Route::post('/list_dfds', 'list_dfds');
        Route::get('/list_dfd_items/{id}', 'list_dfd_items');
        Route::get('/fetch_etp/{process}', 'fetch_etp');
    });

    Route::prefix('/comissionmembers/{comission}')->controller(ComissionMembers::class)->group(function(){
        Route::get('/comission', 'comission');
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

    Route::prefix('/attachments/{origin}/{protocol}')->controller(Attachments::class)->group(function(){
        Route::get('/download_png/{id}', 'download_as_png');
    });
});


// fallback 404
Route::fallback(function () {
    return response()->json(Notify::warning('Destino solicitado não existe...'), 404);
});
