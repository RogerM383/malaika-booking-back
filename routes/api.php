<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\DBMigrationController;
use App\Http\Controllers\DepartureController;
//use App\Http\Controllers\FormController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('client-types')->group(function () {
    Route::get('/', [ClientTypeController::class, 'get'])->middleware('auth:api');
});

Route::prefix('clients')->group(function () {
    Route::get('/', [ClientController::class, 'get']);//->middleware('auth:api');
    Route::get('/{id}', [ClientController::class, 'getById'])->middleware('auth:api');
    Route::get('/{id}/departures', [ClientController::class, 'getClientDepartures'])->middleware('auth:api');
    Route::post('/', [ClientController::class, 'create'])->middleware('auth:api');
    Route::put('/{id}', [ClientController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/{id}', [ClientController::class, 'delete'])->middleware('auth:api');
});

Route::prefix('passports')->group(function () {
    //Route::get('/', [PassportController::class, 'get'])->middleware('auth:api');
    Route::get('/{id}', [PassportController::class, 'getById'])->middleware('auth:api');
    Route::post('/', [PassportController::class, 'create'])->middleware('auth:api');
    Route::put('/{id}', [PassportController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/{id}', [PassportController::class, 'delete'])->middleware('auth:api');
});

Route::prefix('trips')->group(function () {
    Route::get('/', [TripController::class, 'get'])->middleware('auth:api');
    Route::post('/', [TripController::class, 'create'])->middleware('auth:api');
    Route::get('/{id}', [TripController::class, 'getById'])->middleware('auth:api');
    Route::get('/form/{id}', [TripController::class, 'getBySlug'])->middleware('auth:api');
    Route::delete('/{id}', [TripController::class, 'delete'])->middleware('auth:api');
    //Route::post('/', [TripController::class, 'create'])->middleware('auth:api');
    Route::put('/{id}', [TripController::class, 'update'])->middleware(['auth:api']);
});

Route::prefix('departures')->group(function () {
    Route::get('/', [DepartureController::class, 'get'])->middleware('auth:api');
    Route::get('/{id}', [DepartureController::class, 'getById'])->middleware('auth:api');
    Route::post('/', [DepartureController::class, 'create'])->middleware('auth:api');
    Route::put('/{id}', [DepartureController::class, 'update'])->middleware(['auth:api']);
    Route::delete('/{id}', [DepartureController::class, 'delete'])->middleware('auth:api');

    Route::post('/{id}/add-clients', [DepartureController::class, 'addClients'])->middleware('auth:api');
    Route::post('/{id}/add-client', [DepartureController::class, 'addClient'])->middleware('auth:api');
    Route::put('/{id}/client/{client_id}', [DepartureController::class, 'updateDepartureClient'])->middleware('auth:api');
    Route::delete('/{id}/client/{client_id}', [DepartureController::class, 'removeClient'])->middleware('auth:api');

    Route::get('/{id}/rooming', [DepartureController::class, 'getDepartureRooming'])->middleware('auth:api');
});

Route::prefix('forms')->group(function () {
    Route::post('/process', [FormController::class, 'process'])->middleware(['auth:api']);
    Route::get('/trips/{slug}', [FormController::class, 'getFormTripBySlug']);
});

Route::prefix('db')->group(function () {
    Route::get('/', [DBMigrationController::class, 'migrate'])->middleware(['auth:api']);
    Route::get('/2', [DBMigrationController::class, 'migrate2'])->middleware(['auth:api']);
});

Route::prefix('exports')->group(function () {
    Route::get('/departure/{id}', [ExportController::class, 'departure'])->middleware(['auth:api']);
});

Route::prefix('languages')->group(function () {
    Route::get('/', [LanguageController::class, 'get'])->middleware(['auth:api']);
    Route::get('/{id}', [LanguageController::class, 'getById'])->middleware(['auth:api']);
});

Route::prefix('room-types')->group(function () {
    Route::get('/', [RoomTypeController::class, 'get'])->middleware(['auth:api']);
    Route::get('/{id}', [RoomTypeController::class, 'getById'])->middleware(['auth:api']);
});
