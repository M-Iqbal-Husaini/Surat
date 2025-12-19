<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SuratMasukController as AdminSuratMasukController;
use App\Http\Controllers\Admin\SuratKeluarController as AdminSuratKeluarController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TemplateSuratController;
use App\Http\Controllers\Admin\StrukturUnitController;
use App\Http\Controllers\Admin\JalurSuratController;
use App\Http\Controllers\PembuatSurat\SuratMasukController as PembuatSuratMasukController;
use App\Http\Controllers\PembuatSurat\SuratKeluarController as PembuatSuratKeluarController;
use App\Http\Controllers\Verifikator\SuratMasukController as VerifikatorSuratMasukController;
use App\Http\Controllers\Verifikator\SuratKeluarController as VerifikatorSuratKeluarController;
use App\Http\Controllers\SekretarisUnit\SuratMasukController as SekretarisUnitSuratMasukController;
use App\Http\Controllers\SekretarisUnit\SuratKeluarController as SekretarisUnitSuratKeluarController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD MULTI-ROLE
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {

            Route::get('/surat-masuk', [AdminSuratMasukController::class, 'index'])
                ->name('surat-masuk.index')
                ->middleware('permission:view surat masuk');

            Route::get('/surat-keluar', [AdminSuratKeluarController::class, 'index'])
                ->name('surat-keluar.index')
                ->middleware('permission:view surat keluar');

            Route::get('/users', [UserController::class, 'index'])
                ->name('users.index')
                ->middleware('permission:manage users');

            Route::get('/template-surat', [TemplateSuratController::class, 'index'])
                ->name('template-surat.index')
                ->middleware('permission:manage templates');

            Route::get('/struktur-unit', [StrukturUnitController::class, 'index'])
                ->name('struktur-unit.index')
                ->middleware('permission:manage struktur unit');

            Route::get('/jalur-surat', [JalurSuratController::class, 'index'])
                ->name('jalur-surat.index')
                ->middleware('permission:manage jalur surat');
        });

    Route::prefix('pembuat-surat')
        ->name('pembuat-surat.')
        ->middleware('role:pembuat_surat') 
        ->group(function () {

            Route::get('/surat-masuk', [PembuatSuratMasukController::class, 'index'])
                ->name('surat-masuk.index')
                ->middleware('permission:view surat masuk');

            Route::get('/surat-keluar', [PembuatSuratKeluarController::class, 'index'])
                ->name('surat-keluar.index')
                ->middleware('permission:view own surat');

            Route::get('/surat-keluar/create', [PembuatSuratKeluarController::class, 'create'])
                ->name('surat-keluar.create')
                ->middleware('permission:create surat');

            Route::post('/surat-keluar', [PembuatSuratKeluarController::class, 'store'])
                ->name('surat-keluar.store')
                ->middleware('permission:create surat');

            Route::get('/surat-keluar/{surat}/edit', [PembuatSuratKeluarController::class, 'edit'])
                ->name('surat-keluar.edit')
                ->middleware('permission:edit own surat');

            Route::put('/surat-keluar/{surat}', [PembuatSuratKeluarController::class, 'update'])
                ->name('surat-keluar.update')
                ->middleware('permission:edit own surat');
        });

    Route::prefix('verifikator')
        ->name('verifikator.')
        ->middleware('role:verifikator')
        ->group(function () {

            Route::get('/surat-masuk', [VerifikatorSuratMasukController::class, 'index'])
                ->name('surat-masuk.index')
                ->middleware('permission:view surat masuk');

            Route::get('/surat-keluar', [VerifikatorSuratKeluarController::class, 'index'])
                ->name('surat-keluar.index')
                ->middleware('permission:view surat keluar');
        });
    
    Route::prefix('sekretaris-unit')
        ->name('sekretaris-unit.')
        ->middleware('role:sekretaris_unit')
        ->group(function () {

            Route::get('/surat-masuk', [SekretarisUnitSuratMasukController::class, 'index'])
                ->name('surat-masuk.index')
                ->middleware('permission:view surat masuk');

            Route::get('/surat-keluar', [SekretarisUnitSuratKeluarController::class, 'index'])
                ->name('surat-keluar.index')
                ->middleware('permission:view surat keluar');
        });

    Route::prefix('pimpinan')
        ->name('pimpinan.')
        ->middleware('role:pimpinan')
        ->group(function () {

            
        });
});
