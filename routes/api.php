<?php

use App\Http\Controllers\Api\ImportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\SlsController;
use App\Http\Controllers\Api\RutaController;

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

Route::middleware('web')->get('authenticate', [ImportController::class, 'authenticate']);

Route::resource('sls', SlsController::class);
Route::get('make_roles', [ImportController::class, 'make_roles']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('import_user', [ImportController::class, 'import_user']);
    Route::get('export_alokasi', [ExportController::class, 'export_alokasi']);
    Route::post('import_alokasi', [ImportController::class, 'import_alokasi']);
    Route::resource('ruta', RutaController::class);
    Route::post('ruta/many', [RutaController::class, 'store_many']);
});
