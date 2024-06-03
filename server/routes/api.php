<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\CatalogItems;
use App\Http\Controllers\Catalogs;
use App\Http\Controllers\Comissions;
use App\Http\Controllers\ComissionsEnds;
use App\Http\Controllers\ComissionsMembers;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Demandants;
use App\Http\Controllers\Dfds;
use App\Http\Controllers\Dotations;
use App\Http\Controllers\Management;
use App\Http\Controllers\Ordinators;
use App\Http\Controllers\Organs;
use App\Http\Controllers\Programs;
use App\Http\Controllers\Suppliers;
use App\Http\Controllers\Units;
use App\Http\Controllers\Sectors;
use App\Http\Middleware\CheckPermission;
use App\Utils\Notify;
use Illuminate\Support\Facades\Route;


Route::controller(Auth::class)->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::post('', 'auth');
        Route::post('/verify', 'verify');
        Route::post('/recover', 'recover');
        Route::post('/renew', 'renew');
        Route::get('/check', 'check');
    });

})->name('auth');

Route::controller(Dashboard::class)->group(function () {

    Route::prefix('/dashboard')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
        });
    });

})->name('dashboard');

Route::controller(Organs::class)->group(function () {

    Route::prefix('/organs')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/selects', 'selects');
        });
    });
})->name('organs');

Route::controller(Units::class)->group(function () {

    Route::prefix('/units')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/selects', 'selects');
        });
    });
})->name('units');

Route::controller(Sectors::class)->group(function () {

    Route::prefix('/sectors')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/selects/{key?}/{search?}', 'selects');
        });
    });
})->name('sectors');

Route::controller(Ordinators::class)->group(function () {

    Route::prefix('/ordinators')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/download/{id}', 'download');
            Route::get('/selects/{key?}/{search?}', 'selects');
        });
    });
})->name('ordinators');

Route::controller(Demandants::class)->group(function () {

    Route::prefix('/demandants')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/download/{id}', 'download');
            Route::get('/selects/{key?}/{search?}', 'selects');
        });
    });
})->name('demandants');

Route::controller(Comissions::class)->group(function () {

    Route::prefix('/comissions')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/download/{id}', 'download');
            Route::get('/selects/{key?}/{search?}', 'selects');
        });
    });
})->name('comissions');

Route::controller(ComissionsMembers::class)->group(function () {

    Route::prefix('/comissionsmembers')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('/{comission}', 'index');
            Route::post('/list/{comission}', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/download/{id}', 'download');
            Route::get('/{comission}/selects', 'selects');
        });
    });
})->name('comissionsmembers');

Route::controller(ComissionsEnds::class)->group(function () {

    Route::prefix('/comissionsends')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/download/{id}', 'download');
        });
    });
})->name('comissionsends');

Route::controller(Programs::class)->group(function () {

    Route::prefix('/programs')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/selects/{key?}/{search?}', 'selects');
        });
    });
})->name('programs');

Route::controller(Dotations::class)->group(function () {

    Route::prefix('/dotations')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/selects/{key?}/{search?}', 'selects');
        });
    });
})->name('dotations');

Route::controller(Management::class)->group(function () {

    Route::prefix('/users')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/selects', 'selects');
        });
    });
})->name('users');

Route::controller(Catalogs::class)->group(function () {

    Route::prefix('/catalogs')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/selects/{key?}/{search?}', 'selects');
        });
    });
})->name('catalogs');

Route::controller(Suppliers::class)->group(function () {

    Route::prefix('/suppliers')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
        });
    });
})->name('suppliers');

Route::controller(CatalogItems::class)->group(function () {

    Route::prefix('/catalogitems')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('/{catalog}', 'index');
            Route::post('/{catalog}/list', 'list');
            Route::post('/{catalog}/save', 'save');
            Route::put('/{catalog}/update', 'update');
            Route::post('/{catalog}/destroy', 'delete');
            Route::get('/{catalog}/details/{id}', 'details');
            Route::get('/{catalog}/catalog', 'catalog');
            Route::get('/{catalog}/selects/{key?}/{search?}', 'selects');
        });
    });
})->name('catalogitems');

Route::controller(Dfds::class)->group(function () {
    
    Route::prefix('/dfds')->group(function () {
        Route::middleware(CheckPermission::class)->group(function () {
            Route::get('', 'index');
            Route::post('/list', 'list');
            Route::post('/save', 'save');
            Route::post('/generate', 'generate');
            Route::put('/update', 'update');
            Route::post('/destroy', 'delete');
            Route::get('/details/{id}', 'details');
            Route::get('/selects/{key?}/{search?}', 'selects');
            Route::post('/items', 'items');
        });
    });
})->name('dfds');

Route::fallback(function () {
    return Response()->json(Notify::warning('Destino solicitado não existe...'), 404);
});