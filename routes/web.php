<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TtdController;
use App\Http\Controllers\Admin\SuratMasukController as AdminSuratMasukController;
use App\Http\Controllers\Admin\SuratKeluarController as AdminSuratKeluarController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TemplateSuratController;
use App\Http\Controllers\Admin\TemplateFieldController;
use App\Http\Controllers\Admin\StrukturUnitController;
use App\Http\Controllers\Admin\JalurSuratController;
use App\Http\Controllers\PembuatSurat\SuratMasukController as PembuatSuratMasukController;
use App\Http\Controllers\PembuatSurat\SuratKeluarController as PembuatSuratKeluarController;
use App\Http\Controllers\PembuatSurat\DisposisiController as PembuatDisposisiController;
use App\Http\Controllers\Verifikator\SuratMasukController as VerifikatorSuratMasukController;
use App\Http\Controllers\Verifikator\SuratKeluarController as VerifikatorSuratKeluarController;
use App\Http\Controllers\Verifikator\DisposisiController as VerifikatorDisposisiController;
use App\Http\Controllers\SekretarisDirektur\SuratMasukController as SekretarisDirekturSuratMasukController;
use App\Http\Controllers\SekretarisDirektur\SuratKeluarController as SekretarisDirekturSuratKeluarController;
use App\Http\Controllers\SekretarisDirektur\DisposisiController as SekretarisDirekturDisposisiController;
use App\Http\Controllers\Pimpinan\SuratMasukController as PimpinanSuratMasukController;
use App\Http\Controllers\Pimpinan\SuratKeluarController as PimpinanSuratKeluarController;
use App\Http\Controllers\Pimpinan\DisposisiController as PimpinanDisposisiController;

Route::get('/', fn () => redirect('/login'));

Route::get('/surat-status/{surat}', function (App\Models\Surat $surat) {
    return response()->json([
        'status' => $surat->status,
    ]);
})->name('surat.status');

Route::get(
    '/verifikasi-ttd/{token}',
    [PembuatSuratMasukController::class, 'verifikasiTtd']
)->name('surat.verifikasi.ttd');

Route::get(
    '/qr/ttd/{token}',
    [PembuatSuratKeluarController::class, 'qrTtd']
)->name('surat.qr.ttd');

Route::get('/surat/verifikasi/{token}', function ($token) {
    $surat = Surat::where('signed_token', $token)->firstOrFail();

    return view('surat.verifikasi', compact('surat'));
})->name('surat.verify');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {

            Route::prefix('template-surat')
                ->name('template-surat.')
                ->middleware('permission:manage templates')
                ->group(function () {

                    Route::get('/', [TemplateSuratController::class, 'index'])->name('index');
                    Route::get('/create', [TemplateSuratController::class, 'create'])->name('create');
                    Route::post('/', [TemplateSuratController::class, 'store'])->name('store');
                    Route::get('/{template}', [TemplateSuratController::class, 'show'])->name('show');
                    Route::get('/{template}/edit', [TemplateSuratController::class, 'edit'])->name('edit');
                    Route::put('/{template}', [TemplateSuratController::class, 'update'])->name('update');
                    Route::delete('/{template}', [TemplateSuratController::class, 'destroy'])->name('destroy');

                    Route::prefix('{template}/fields')
                        ->name('template-fields.')
                        ->group(function () {

                            Route::get('/', [TemplateFieldController::class, 'index'])->name('index');
                            Route::get('/create', [TemplateFieldController::class, 'create'])->name('create');
                            Route::post('/', [TemplateFieldController::class, 'store'])->name('store');
                            Route::get('/{field}/edit', [TemplateFieldController::class, 'edit'])->name('edit');
                            Route::put('/{field}', [TemplateFieldController::class, 'update'])->name('update');
                            Route::delete('/{field}', [TemplateFieldController::class, 'destroy'])->name('destroy');
                        });
                });

            Route::prefix('jalur-surat')
                ->name('jalur-surat.')
                ->middleware('permission:manage jalur surat')
                ->group(function () {

                    Route::get('/', [JalurSuratController::class, 'index'])->name('index');
                    Route::get('/create', [JalurSuratController::class, 'create'])->name('create');
                    Route::post('/', [JalurSuratController::class, 'store'])->name('store');
                    Route::get('/{alur}/edit', [JalurSuratController::class, 'edit'])->name('edit');
                    Route::put('/{alur}', [JalurSuratController::class, 'update'])->name('update');
                    Route::delete('/{alur}', [JalurSuratController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('struktur-unit')
                ->name('struktur-unit.')
                ->middleware('permission:manage struktur unit')
                ->group(function () {

                    Route::get('/', [StrukturUnitController::class, 'index'])->name('index');
                    Route::get('/create', [StrukturUnitController::class, 'create'])->name('create');
                    Route::post('/', [StrukturUnitController::class, 'store'])->name('store');
                    Route::get('/{unit}', [StrukturUnitController::class, 'show'])->name('show');
                    Route::get('/{unit}/edit', [StrukturUnitController::class, 'edit'])->name('edit');
                    Route::put('/{unit}', [StrukturUnitController::class, 'update'])->name('update');
                    Route::delete('/{unit}', [StrukturUnitController::class, 'destroy'])->name('destroy');
                });

            Route::get('/surat-keluar', [JalurSuratController::class, 'index'])
                ->name('surat-keluar.index')
                ->middleware('permission:manage surat keluar');

            Route::get('/surat-masuk', [JalurSuratController::class, 'index'])
                ->name('surat-masuk.index')
                ->middleware('permission:manage surat masuk');

            Route::get('/surat-masuk', [AdminSuratMasukController::class, 'index'])
                ->name('surat-masuk.index')
                ->middleware('permission:view surat masuk');

            Route::get('/surat-keluar', [AdminSuratKeluarController::class, 'index'])
                ->name('surat-keluar.index')
                ->middleware('permission:view surat keluar');

            Route::get('/surat-masuk/{surat}', [AdminSuratMasukController::class, 'show'])
                ->name('surat-masuk.show')
                ->middleware('permission:view surat masuk');

            Route::get('/surat-keluar/{surat}', [AdminSuratKeluarController::class, 'show'])
                ->name('surat-keluar.show')
                ->middleware('permission:view surat keluar');



            Route::prefix('users')
                ->name('users.')
                ->middleware('permission:manage users')
                ->group(function () {

                    Route::get('/', [UserController::class, 'index'])->name('index');
                    Route::get('/create', [UserController::class, 'create'])->name('create');
                    Route::post('/', [UserController::class, 'store'])->name('store');
                    Route::get('/{user}', [UserController::class, 'show'])->name('show');
                    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                    Route::put('/{user}', [UserController::class, 'update'])->name('update');
                    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
                });
        });

    Route::prefix('pembuat-surat')
        ->name('pembuat-surat.')
        ->middleware('role:pembuat_surat')
        ->group(function () {

            Route::prefix('surat-masuk')
                ->name('surat-masuk.')
                ->middleware('permission:view surat masuk')
                ->group(function () {

                    Route::get('/', [PembuatSuratMasukController::class, 'index'])->name('index');
                    Route::get('/{surat}', [PembuatSuratMasukController::class, 'show'])->name('show');
                    Route::post('/{surat}/terima', [PembuatSuratMasukController::class, 'terima'])->name('terima');
                    Route::post('/{surat}/tolak', [PembuatSuratMasukController::class, 'tolak'])->name('tolak');
                    Route::post('/{surat}/direvisi', [PembuatSuratMasukController::class, 'direvisi'])->name('direvisi');
                    Route::post('/{surat}/finalisasi', [PembuatSuratMasukController::class, 'finalisasi'])->name('finalisasi');
                    Route::post('/{surat}/disposisi', [PembuatSuratMasukController::class, 'disposisi'])->name('disposisi');
                    Route::post('/{surat}/tindak-lanjut', [PembuatSuratMasukController::class, 'tindaklanjuti'])->name('tindaklanjuti');
                });

            Route::get('/surat-keluar', [PembuatSuratKeluarController::class, 'index'])->name('surat-keluar.index');
            Route::get('/surat-keluar/create', [PembuatSuratKeluarController::class, 'create'])->name('surat-keluar.create');
            Route::post('/surat-keluar', [PembuatSuratKeluarController::class, 'store'])->name('surat-keluar.store');
            Route::get('/surat-keluar/{surat}/edit', [PembuatSuratKeluarController::class, 'edit'])->name('surat-keluar.edit');
            Route::put('/surat-keluar/{surat}', [PembuatSuratKeluarController::class, 'update'])->name('surat-keluar.update');
            Route::get('/surat-keluar/{surat}', [PembuatSuratKeluarController::class, 'show'])->name('surat-keluar.show');
            Route::get('/surat-keluar/{surat}/preview', [PembuatSuratKeluarController::class, 'preview'])->name('surat-keluar.preview');
            Route::post('/surat-keluar/{surat}/ajukan', [PembuatSuratKeluarController::class, 'ajukan'])->name('surat-keluar.diajukan'); 
            Route::post('/surat-keluar/{surat}/ttd', [PembuatSuratKeluarController::class, 'ttd'])->name('surat-keluar.ttd'); 

            Route::prefix('disposisi')
                ->name('disposisi.')
                ->group(function () {
                    Route::get('/', [PembuatDisposisiController::class, 'index'])->name('index');
                    Route::get('/{disposisi}', [PembuatDisposisiController::class, 'show'])->name('show');
                    Route::post('/{disposisi}/terima', [PembuatDisposisiController::class, 'terima'])->name('terima');
                    Route::post('/{disposisi}/tolak', [PembuatDisposisiController::class, 'tolak'])->name('tolak');
                    Route::post(
                        '/{surat}/tindaklanjuti',
                        [PembuatDisposisiController::class, 'tindaklanjuti']
                    )->name('tindaklanjuti');
                });


        });

    Route::middleware(['auth', 'verified', 'role:verifikator'])
        ->prefix('verifikator')
        ->name('verifikator.')
        ->group(function () {

            Route::get('/surat-masuk', [VerifikatorSuratMasukController::class, 'index'])
                ->name('surat-masuk.index');

            Route::get('/surat-masuk/{surat}', [VerifikatorSuratMasukController::class, 'show'])
                ->name('surat-masuk.show');

            Route::post('/surat-masuk/{surat}/setujui', [VerifikatorSuratMasukController::class, 'setujui'])
                ->name('surat-masuk.setujui');

            Route::post('/surat-masuk/{surat}/tolak', [VerifikatorSuratMasukController::class, 'tolak'])
                ->name('surat-masuk.tolak');

            Route::post('/surat-masuk/{surat}/revisi', [VerifikatorSuratMasukController::class, 'revisi'])
                ->name('surat-masuk.revisi');

            Route::post('/surat-masuk/{surat}/ttd', [VerifikatorSuratMasukController::class, 'ttd'])
                ->name('surat-masuk.ttd');

            Route::get('/surat-keluar', [VerifikatorSuratKeluarController::class, 'index'])
                ->name('surat-keluar.index');

            Route::get('surat-keluar', [VerifikatorSuratKeluarController::class, 'index'])
                ->name('surat-keluar.index');

            Route::get('surat-keluar/{surat}', [VerifikatorSuratKeluarController::class, 'show'])
                ->name('surat-keluar.show');

        });

    Route::prefix('sekretaris-direktur')
        ->name('sekretaris-direktur.')
        ->middleware('role:sekretaris_direktur')
        ->group(function () {

            Route::get('/surat-masuk', [SekretarisDirekturSuratMasukController::class, 'index'])->name('surat-masuk.index');
            Route::get('/surat-keluar', [SekretarisDirekturSuratKeluarController::class, 'index'])->name('surat-keluar.index');
            Route::get('/surat-masuk/{surat}', [SekretarisDirekturSuratMasukController::class, 'show'])->name('surat-masuk.show');
            Route::post('/{surat}/setujui', [SekretarisDirekturSuratMasukController::class, 'setujui'])->name('surat-masuk.setujui');
            Route::post('/{surat}/tolak', [SekretarisDirekturSuratMasukController::class, 'tolak'])->name('surat-masuk.tolak');
            Route::post('/{surat}/direvisi', [SekretarisDirekturSuratMasukController::class, 'revisi'])->name('surat-masuk.revisi');
            Route::post('/{surat}/disposisi', [SekretarisDirekturSuratMasukController::class, 'disposisi'])->name('surat-masuk.disposisi');
            Route::post('/{surat}/tindak-lanjut', [SekretarisDirekturSuratMasukController::class, 'tindaklanjuti'])->name('surat-masuk.tindaklanjuti');

            Route::get('surat-keluar', [SekretarisDirekturSuratKeluarController::class, 'index'])
                ->name('surat-keluar.index');

            Route::get('surat-keluar/{surat}', [SekretarisDirekturSuratKeluarController::class, 'show'])
                ->name('surat-keluar.show');
        });

    Route::prefix('pimpinan')
        ->name('pimpinan.')
        ->middleware('role:pimpinan')
        ->group(function () {

            Route::get('/surat-masuk', [PimpinanSuratMasukController::class, 'index'])->name('surat-masuk.index');
            Route::get('/surat-masuk/{surat}', [PimpinanSuratMasukController::class, 'show'])->name('surat-masuk.show');
            Route::post('/surat-masuk/{surat}/setujui', [PimpinanSuratMasukController::class, 'setujui'])->name('surat-masuk.setujui');
            Route::post('/surat-masuk/{surat}/tolak', [PimpinanSuratMasukController::class, 'tolak'])->name('surat-masuk.tolak');
            Route::post('/surat-masuk/{surat}/disposisi', [PimpinanSuratMasukController::class, 'disposisi'])->name('surat-masuk.disposisi');
            Route::post('/surat-masuk/{surat}/ttd', [PimpinanSuratMasukController::class, 'ttd'])->name('surat-masuk.ttd');

            Route::get('/surat-keluar', [PimpinanSuratKeluarController::class, 'index'])->name('surat-keluar.index');
            Route::get('/surat-keluar/{surat}', [PimpinanSuratKeluarController::class, 'show'])->name('surat-keluar.show');
            Route::post('/surat-keluar/{surat}/kirim', [PimpinanSuratKeluarController::class, 'kirim'])->name('surat-keluar.kirim');

        });
});
