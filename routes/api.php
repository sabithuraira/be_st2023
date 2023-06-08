<?php

use App\Http\Controllers\Api\AlokasiController;
use App\Http\Controllers\Api\ImportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\PetugasController;
use App\Http\Controllers\Api\SlsController;
use App\Http\Controllers\Api\RutaController;
use App\Http\Controllers\Api\WIlayahController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('cd/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('change_password', [AuthController::class, 'change_password']);
Route::middleware('web')->get('authenticate', [ImportController::class, 'authenticate']);
Route::middleware('web')->get('getcsrf', [PetugasController::class, 'getcsrf']);
// Route::middleware('web')->resource('petugas', PetugasController::class);
Route::resource('sls', SlsController::class);
Route::get('sls/{jenis}/{kode_petugas}/petugas', [SlsController::class, 'get_by_petugas']);
Route::get('sls/{kode_sls}/sls', [SlsController::class, 'get_by_sls']);
Route::get('make_roles', [ImportController::class, 'make_roles']);

Route::get('list_kabs', [WIlayahController::class, 'list_kabs']);
Route::get('list_kecs', [WIlayahController::class, 'list_kecs']);
Route::get('list_desas', [WIlayahController::class, 'list_desas']);
Route::get('list_sls', [WIlayahController::class, 'list_sls']);
Route::get('list_roles', [PetugasController::class, 'list_roles']);


Route::get('delete_ruta_duplikat', [RutaController::class, 'delete_ruta_duplikat']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('progress', [DashboardController::class, 'progress']);
    Route::get('progress_kk', [DashboardController::class, 'progress_kk']);
    Route::get('progress_dokumen', [DashboardController::class, 'progress_dokumen']);

    Route::get('dashboard_waktu', [DashboardController::class, 'dashboard_waktu']);
    Route::get('dashboard_lokasi', [DashboardController::class, 'dashboard_lokasi']);
    Route::get('dashboard_target', [DashboardController::class, 'dashboard_target']);

    Route::get('dashboard_koseka', [DashboardController::class, 'dashboard_koseka']);

    Route::resource('petugas', PetugasController::class);
    Route::get('petugas/data/rekap', [PetugasController::class, 'rekap']);

    Route::get('petugas_sls/{id}', [PetugasController::class, 'petugas_sls']);
    Route::get('list_petugas', [PetugasController::class, 'list_petugas']);
    // Route::get('list_pcl', [PetugasController::class, 'list_pcl']);
    // Route::get('list_pml', [PetugasController::class, 'list_pml']);
    // Route::get('list_koseka', [PetugasController::class, 'list_koseka']);

    Route::resource('alokasi', AlokasiController::class);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('import_user', [ImportController::class, 'import_user']);

    Route::get('export_alokasi', [ExportController::class, 'export_alokasi']);
    Route::get('export_ruta', [ExportController::class, 'export_ruta']);
    Route::get('export_progress', [ExportController::class, 'export_progress']);


    Route::get('export_target', [ExportController::class, 'export_target']);
    Route::post('import_alokasi', [ImportController::class, 'import_alokasi']);
    Route::post('import_ruta_regsosek', [ImportController::class, 'import_ruta_regsosek']);

    Route::resource('ruta', RutaController::class);
    Route::post('ruta/many', [RutaController::class, 'store_many']);

    Route::post('sls/update_progress', [SlsController::class, 'update_progress']);
});
