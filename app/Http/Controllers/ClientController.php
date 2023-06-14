<?php

namespace App\Http\Controllers;

use App\Exceptions\ClientNotFoundException;
use App\Http\Controllers\Interfaces\ResourceControllerInterface;
use App\Http\Resources\ClientListCollection;
use App\Http\Resources\ClientListResource;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller implements ResourceControllerInterface
{
    use HasPagination;

    private ClientService $service;

    public function __construct(ClientService $clietnService)
    {
        $this->service = $clietnService;
    }

    /**
     * @OA\Get(
     *      path="/api/clients",
     *      tags={"Clients"},
     *      summary="Lista de clientes",
     *      security={{"bearer_token":{}}},
     *      description="Lista los clientes",
     *      operationId="clientList",
     *      @OA\Parameter(
     *          name="client_type",
     *          in="query",
     *          description="Id de tipo de cliente",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Nombre del cliente",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="surname",
     *          in="query",
     *          description="Apellido del cliente",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="phone",
     *          in="query",
     *          description="Teléfono del cliente",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          in="query",
     *          description="Email del cliente",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="dni",
     *          in="query",
     *          description="DNI del cliente",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="passport",
     *          in="query",
     *          description="Número de pasaporte del cliente",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          description="Número de elementos por página",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="Número de página",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client list retrieves successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/ClientListResource")
     *              )
     *          )
     *      )
     *  )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function get(Request $request): JsonResponse
    {
        $validatedData = Validator::make($request->all(), [
            'client_type'   => 'integer',
            'name'          => 'string',
            'surname'       => 'string',
            'phone'         => 'string',
            'email'         => 'string',
            'dni'           => 'string',
            'passport'      => 'string',
            'per_page'      => 'integer|min:1',
            'page'          => 'integer|min:1'
        ])->validate();

        if ($this->isPaginated(...$request->only('per_page', 'page'))) {
            $data = new ClientListCollection($this->service->all(...$validatedData));
        } else {
            $data = ClientListResource::collection($this->service->all(...$validatedData));
        }

        return $this->sendResponse($data,'Client list retrieved successfully');
    }

    /**
     * @OA\Get(
     *      path="/api/clients/{id}",
     *      tags={"Clients"},
     *      summary="Retorna un cliente por ID",
     *      security={{"bearer_token":{}}},
     *      description="Retorna un cliente",
     *      operationId="getClientById",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client retrived successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/ClientResource"
     *              )
     *          )
     *      )
     *  )
     * @throws ClientNotFoundException
     */
    public function getById(Request $request, $id): JsonResponse
    {
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ])->validate();

        return $this->sendResponse(
            new ClientResource($this->service->getById($validatedData['id'])),
            'Client retrieved successfully'
        );
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

    /**
     * @OA\Post(
     *      path="/api/clients/{id}",
     *      tags={"Clients"},
     *      summary="Actualiza los datos del cliente",
     *      security={{"bearer_token":{}}},
     *      description="Actualiza los datos del cliente",
     *      operationId="updateClient",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *          description="Update client",
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
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client updated successfully",
     *      ),
     *  )
     * @throws ValidationException
     * @throws ClientNotFoundException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $params = array_merge($request->only($this->service->getFillable()), ['id' => $id]);
        Log::debug(json_encode($params));
        $validatedData = Validator::make($params, [
            'id'                => 'required',
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
            new ClientResource($this->service->update($id, $validatedData)),
            'Client updated successfully'
        );
    }
}
