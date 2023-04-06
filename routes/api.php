<?php

use App\Http\Controllers\Api\ImportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\SlsController;
use App\Http\Controllers\API\RutaController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('web')->get('authenticate', [ImportController::class, 'authenticate']);

Route::resource('sls', SlsController::class);
Route::resource('ruta', RutaController::class);
Route::get('make_roles', [ImportController::class, 'make_roles']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('import_user', [ImportController::class, 'import_user']);
});
