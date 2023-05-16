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
Route::get('sls/get_by_petugas', [SlsController::class, 'get_by_petugas']);
Route::get('sls/{jenis}/{kode_petugas}/petugas', [SlsController::class, 'get_by_petugas']);
Route::get('make_roles', [ImportController::class, 'make_roles']);

Route::get('list_kabs', [WIlayahController::class, 'list_kabs']);
Route::get('list_kecs', [WIlayahController::class, 'list_kecs']);
Route::get('list_desas', [WIlayahController::class, 'list_desas']);
Route::get('list_sls', [WIlayahController::class, 'list_sls']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('progress', [DashboardController::class, 'progress']);
    Route::get('progress_kk', [DashboardController::class, 'progress_kk']);
    Route::get('progress_dokumen', [DashboardController::class, 'progress_dokumen']);

    Route::resource('petugas', PetugasController::class);
    Route::get('list_petugas', [PetugasController::class, 'list_petugas']);
    // Route::get('list_pcl', [PetugasController::class, 'list_pcl']);
    // Route::get('list_pml', [PetugasController::class, 'list_pml']);
    // Route::get('list_koseka', [PetugasController::class, 'list_koseka']);

    Route::resource('alokasi', AlokasiController::class);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('import_user', [ImportController::class, 'import_user']);

    Route::get('export_alokasi', [ExportController::class, 'export_alokasi']);
    Route::post('import_alokasi', [ImportController::class, 'import_alokasi']);

    Route::resource('ruta', RutaController::class);
    Route::post('ruta/many', [RutaController::class, 'store_many']);

    Route::post('sls/update_progress', [SlsController::class, 'update_progress']);
});
