<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\Client\ClientDetailResource;
use App\Http\Resources\Client\ClientListCollection;
use App\Http\Resources\Client\ClientListResource;
use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Departure\DepartureClientListResource;
use App\Http\Resources\Departure\DepartureListResource;
use App\Http\Resources\Departure\DepartureResource;
use App\Services\ClientService;
use App\Services\DatabaseMigrationService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
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
            $data = new ClientListCollection($this->service->get(...$validatedData));
        } else {
            $data = ClientListResource::collection($this->service->get(...$validatedData));
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
     * @throws ValidationException|ModelNotFoundException
     */
    public function getById(Request $request, $id): JsonResponse
    {
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ])->validate();

        return $this->sendResponse(
            new ClientDetailResource($this->service->getById($validatedData['id'])),
            'Client retrieved successfully'
        );
    }

    /**
     * @OA\Get(
     *      path="/api/clients/{id}/departures",
     *      tags={"Clients"},
     *      summary="Retorna las salidas de un cliente",
     *      security={{"bearer_token":{}}},
     *      description="Retorna las salidas un cliente",
     *      operationId="getClientDepartures",
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
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/DepartureResource")
     *              )
     *          )
     *      )
     *  )
     * @throws ValidationException|ModelNotFoundException
     */
    public function getClientDepartures(Request $request, $id): JsonResponse
    {
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ])->validate();

        return $this->sendResponse(
            DepartureClientListResource::collection($this->service->getClientDepartures($validatedData['id'])),
            'Client departures retrieved successfully'
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
     *              @OA\Property(property="client_type_id", type="integer", example="1"),
     *              @OA\Property(property="language_id", type="integer", example="1"),
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
    public function create(Request $request): JsonResponse
    {
        $validatedData = Validator::make($request->only($this->service->getFillable()), [
            'client_type_id'    => 'nullable|integer|min:1',
            'language_id'       => 'nullable|integer|min:1',
            'name'              => 'required|string',
            'surname'           => 'nullable|string',
            'phone'             => 'nullable|string',
            'email'             => 'nullable|string|email',
            'dni'               => 'nullable|string',
            'address'           => 'nullable|string',
            'dni_expiration'    => 'nullable|string',
            'place_birth'       => 'nullable|string',
            'intolerances'      => 'nullable|string',
            'frequent_flyer'    => 'nullable|string',
            'member_number'     => 'nullable|string',
            'notes'             => 'nullable|string',
            'observations'      => 'nullable|string',
            'seat'              => 'nullable|string',
            'room_observations' => 'nullable|string'
        ])->validate();

        return $this->sendResponse(
            new ClientResource($this->service->create($validatedData)),
            'Client created successfully'
        );
    }

    /**
     * @OA\Put(
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
     * @throws ValidationException|ModelNotFoundException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $params = array_merge($request->only($this->service->getFillable()), ['id' => $id]);

        $validatedData = Validator::make($params, [
            'client_type_id'    => 'nullable|integer|min:1',
            'language_id'       => 'nullable|integer|min:1',
            'name'              => 'required|string',
            'surname'           => 'nullable|string',
            'phone'             => 'nullable|string',
            'email'             => 'nullable|string|email',
            'dni'               => 'nullable|string',
            'address'           => 'nullable|string',
            'dni_expiration'    => 'nullable|string',
            'place_birth'       => 'nullable|string',
            'intolerances'      => 'nullable|string',
            'frequent_flyer'    => 'nullable|string',
            'member_number'     => 'nullable|string',
            'notes'             => 'nullable|string',
            'observations'      => 'nullable|string',
            'seat'              => 'nullable|string',
            'room_observations' => 'nullable|string'
        ])->validate();

        return $this->sendResponse(
            new ClientResource($this->service->update($id, $validatedData)),
            'Client updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/clients/{id}",
     *      tags={"Clients"},
     *      summary="Elimina un cliente",
     *      security={{"bearer_token":{}}},
     *      description="Elimina un cliente",
     *      operationId="deleteClient",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client deleted successfully",
     *      ),
     *  )
     * @throws ValidationException|ModelNotFoundException
     */
    public function delete(Request $request, $id): JsonResponse
    {
        Validator::make(['id' => $id], ['id' => 'required'])->validate();
        $this->service->delete($id);
        return $this->sendResponse([], 'Trip deleted successfully');
    }
}
