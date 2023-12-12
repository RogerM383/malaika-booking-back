<?php

use App\Http\Controllers\ExportController;
use App\Mail\NewInscriptionClient;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resources([
    'clients' => ClientController::class,
]);

//DATA==================================================================================================================================
Route::get('/departure/{id}', [ExportController::class, 'departure'])->middleware([/*, 'auth:api'*/]);

Route::get('/email', function(){
    return new NewInscriptionClient([
        'title'     => 'Trip title',
        'pdf' => 'pdef',
        'booking_price' => 400,
        'clients'   => [
            [
                'name' => 'Name',
                'surname' => 'Surname',
                'dni' => '478/54123L',
                'MNAC' => '643876743'
            ],
            [
                'name' => 'Name',
                'surname' => 'Surname',
                'dni' => '478/54123L',
                'MNAC' => '643876743'
            ],
            [
                'name' => 'Name',
                'surname' => 'Surname',
                'dni' => '478/54123L',
                'MNAC' => '643876743'
            ]
        ],
        'rooms'     => [
            ['quantity' => 1, 'name' => 'Individual'],
            ['quantity' => 1, 'name' => 'Doble']
        ],
        'contact'   => [
            'name' => 'Name',
            'surname' => 'Surname',
            'phone' => '4587956',
            'email' => 'email@gmail.com'
        ]
    ]);
});
