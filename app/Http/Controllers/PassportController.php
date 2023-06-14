<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use App\Traits\HasPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PassportController extends Controller
{
    use HasPagination;

    private ClientService $service;

    public function __construct(ClientService $clietnService)
    {
        $this->service = $clietnService;
    }

    /**
     * @OA\Post(
     *      path="/api/clients",
     *      tags={"Clients"},
     *      summary="Crea un nuevo cliente",
     *      security={{"bearer_token":{}}},
     *      description="Crea un nuevo cliente",
     *      operationId="createClient",
     *      @OA\Response(
     *          response="200",
     *          description="Client created successfully",
     *      ),
     *     @OA\RequestBody(
     *          description="Create user",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"warehouse_id", "stock"},
     *              @OA\Property(property="name", type="string", example="Selene"),
     *              @OA\Property(property="surname", type="string", example="Selenita"),
     *              @OA\Property(property="phone", type="string", example="6945798"),
     *              @OA\Property(property="email", type="string", example="selene@gmail.com"),
     *              @OA\Property(property="dni", type="string", example="47854123X"),
     *              @OA\Property(property="address", type="string", example="Caella falsa 123, 08194, Barcelona"),
     *              @OA\Property(property="dni_expiration", type="string", example="2025/12/01"),
     *              @OA\Property(property="place_birth", type="string", example="Barcelona"),
     *          )
     *      )
     *  )
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $validatedData = Validator::make($request->only($this->service->getFillable()), [
            'name'              => 'required|string',
            'surname'           => 'string',
            'phone'             => 'string',
            'email'             => 'string|email',
            'dni'               => 'string',
            'address'           => 'string',
            'dni_expiration'    => 'string',
            'place_birth'       => 'string'
        ])->validate();

        return $this->sendResponse(
            new ClientResource($this->service->create($validatedData)),
            'Client created successfully'
        );
    }
}
