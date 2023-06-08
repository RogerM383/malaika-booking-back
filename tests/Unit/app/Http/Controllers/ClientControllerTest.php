<?php

use App\Http\Controllers\ClientController;
use App\Http\Resources\ClientListResource;
use App\Models\Client;
use App\Services\ClientService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class, WithoutMiddleware::class);

it('must retrieve all products', function () {

    $this->seed(DatabaseSeeder::class);

    $service = new ClientService(new Client());
    $clients = $service->all();

    $clientServiceMock = Mockery::mock(ClientService::class);

    //$products = [new Product($product1), new Product($product2)];
    $clientServiceMock->shouldReceive('all')
        ->once()
        //->with(Mockery::type('array'))
        ->andReturn($clients);

    $controller = new ClientController($clientServiceMock);

    $response = $controller->get(new Request());

    $responseMoked = [
        'success' => true,
        'data'    => ClientListResource::collection($clients),
        'message' => 'Client list retrieved successfully'
    ];
    $expectedResponse = response()->json($responseMoked, 200);

    $this->assertEquals($expectedResponse, $response);
});
