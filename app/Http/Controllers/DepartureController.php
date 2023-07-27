<?php

namespace App\Http\Controllers;

use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\Departure\DepartureCollection;
use App\Http\Resources\Departure\DepartureDetailsResource;
use App\Http\Resources\Departure\DepartureExportResource;
use App\Http\Resources\Departure\DepartureResource;
use App\Http\Resources\Departure\DepartureRoomingResource;
use App\Services\DepartureService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DepartureController extends Controller
{
    use HasPagination;

    private DepartureService $service;

    public function __construct(DepartureService $departureService)
    {
        $this->service = $departureService;
    }

    /**
     * @OA\Get(
     *      path="/api/departures",
     *      tags={"Departures"},
     *      summary="Lista las salidas",
     *      security={{"bearer_token":{}}},
     *      description="Lista las salidas",
     *      operationId="departuresList",
     *      @OA\Parameter(
     *          name="trip_id",
     *          in="query",
     *          description="Id de la salida",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *       @OA\Parameter(
     *          name="state",
     *          in="query",
     *          description="Estado de la salida",
     *          required=false,
     *          @OA\Schema(type="integer")
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
     *          description="Departure list retrieved successfully",
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
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function get(Request $request): JsonResponse
    {
        $validatedData = Validator::make($request->all(), [
            'trip_id'       => 'integer|min:1',
            'state'         => 'integer|min:1|max:2',
            'per_page'      => 'integer|min:1',
            'page'          => 'integer|min:1'
        ])->validate();

        if ($this->isPaginated(...$request->only('per_page', 'page'))) {
            $data = new DepartureCollection($this->service->get(...$validatedData));
        } else {
            $data = DepartureResource::collection($this->service->get(...$validatedData));
        }

        return $this->sendResponse($data, 'Departures retrieved successfully');
    }

    /**
     * @OA\Get(
     *      path="/api/departures/{id}",
     *      tags={"Departures"},
     *      summary="Retorna los detalles de una salida",
     *      security={{"bearer_token":{}}},
     *      description="Retorna los detalles de una salida",
     *      operationId="departureData",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de slaida",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Departure retrieved successfully",
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
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function getById(Request $request, $id): JsonResponse
    {
        return $this->sendResponse(
            new DepartureDetailsResource($this->service->getById($id)),
            'Departure retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *      path="/api/departures",
     *      tags={"Departures"},
     *      summary="Crea una nueva salida",
     *      security={{"bearer_token":{}}},
     *      description="Crea una nueva salida",
     *      operationId="createDeparture",
     *     @OA\RequestBody(
     *          description="Create departure",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"trip_id"},
     *              @OA\Property(property="trip_id", type="integer", example="1"),
     *              @OA\Property(property="start", type="string", example="12/20/2023"),
     *              @OA\Property(property="final", type="string", example="12/28/2023"),
     *              @OA\Property(property="price", type="numeric", example="7800.55"),
     *              @OA\Property(property="pax_capacity", type="integer", example="20"),
     *              @OA\Property(property="individual_supplement", type="numeric", example="550.00"),
     *              @OA\Property(property="state_id", type="integer", example="1"),
     *              @OA\Property(property="commentary", type="string", example="Salimos muy temprano"),
     *              @OA\Property(property="expedient", type="integer", example="55688"),
     *              @OA\Property(property="taxes", type="numeric", example="157.65"),
     *              @OA\Property(property="rooms", type="object", example={"1": 10, "2": 10, "3": 0})
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Departure created successfully",
     *      ),
     *  )
     * @throws ValidationException|ModelNotFoundException|DeparturePaxCapacityExceededException
     */
    public function create(Request $request): JsonResponse
    {
        //$params = array_merge($request->only($this->service->getFillable());
        $validatedData = Validator::make($request->all(), [
            'trip_id'               => 'required|integer',
            'start'                 => 'required|string',
            'final'                 => 'required|string',
            'price'                 => 'required|numeric',
            'pax_capacity'          => 'required|integer',
            'individual_supplement' => 'numeric',
            'state_id'              => 'integer|min:1',
            'commentary'            => 'string',
            'expedient'             => 'integer',
            'taxes'                 => 'numeric',

            'rooms'                 => 'array',
            'rooms.*'               => 'integer'
        ])->validate();

        // TODO: Mirar maneras mejores de formatear las fechas, sobretodo ver comop vienen del front
        $validatedData['start'] = date('Y-m-d', strtotime($validatedData['start']));
        $validatedData['final'] = date('Y-m-d', strtotime($validatedData['final']));

        return $this->sendResponse(
            new DepartureResource($this->service->create($validatedData)),
            'Departure created successfully'
        );
    }

    /**
     * @OA\Put(
     *      path="/api/departures/{id}",
     *      tags={"Departures"},
     *      summary="Actualiza los datos de una salida",
     *      security={{"bearer_token":{}}},
     *      description="Actualiza los datos de una salida",
     *      operationId="updateDeparture",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de departure",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *          description="Update departure",
     *          required=true,
     *          @OA\JsonContent(
     *              required={},
     *              @OA\Property(property="start", type="string", example="12/20/2023"),
     *              @OA\Property(property="final", type="string", example="12/28/2023"),
     *              @OA\Property(property="price", type="numeric", example="7800.55"),
     *              @OA\Property(property="pax_capacity", type="integer", example="20"),
     *              @OA\Property(property="individual_supplement", type="numeric", example="550.00"),
     *              @OA\Property(property="state_id", type="integer", example="1"),
     *              @OA\Property(property="commentary", type="string", example="Salimos muy temprano"),
     *              @OA\Property(property="expedient", type="integer", example="55688"),
     *              @OA\Property(property="taxes", type="numeric", example="157.65"),
     *              @OA\Property(property="rooms", type="object", example={"1": 10, "2": 10, "3": 0})
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Departure updated successfully",
     *      ),
     *  )
     * @throws ValidationException
     * @throws ModelNotFoundException|DeparturePaxCapacityExceededException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $params = array_merge(
            $request->only($this->service->getFillable()),
            [
                'id' => $id,
                'rooms' => $request->rooms
            ]
        );
        $validatedData = Validator::make($params, [
            'id'                    => 'required|integer',
            'start'                 => 'string',
            'final'                 => 'string',
            'price'                 => 'numeric',
            'pax_capacity'          => 'integer',
            'individual_supplement' => 'numeric',
            'state_id'              => 'integer|min:1',
            'commentary'            => 'string',
            'expedient'             => 'integer',
            'taxes'                 => 'numeric',

            'rooms'                 => 'array',
            'rooms.*'               => 'integer'
        ])->validate();

        // TODO: Mirar maneras mejores de formatear las fechas, sobretodo ver comop vienen del front
        $validatedData['start'] = date('Y-m-d', strtotime($validatedData['start']));
        $validatedData['final'] = date('Y-m-d', strtotime($validatedData['final']));

        return $this->sendResponse(
            new DepartureExportResource($this->service->update($id, $validatedData)),
            'Departure updated successfully'
        );
    }

    /**
     * @OA\Put(
     *      path="/api/departures/{id}/client/{client_id}",
     *      tags={"Departures"},
     *      summary="Actualiza los datos del cliente en una salida",
     *      security={{"bearer_token":{}}},
     *      description="Actualiza los datos del cliente en una salida",
     *      operationId="updateClientDeparture",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de departure",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="client_id",
     *          in="path",
     *          description="Id del cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *          description="Update client departure data",
     *          required=true,
     *          @OA\JsonContent(
     *              required={},
     *              @OA\Property(property="seat", type="string", example="FINESTRA"),
     *              @OA\Property(property="state", type="integer", example="3"),
     *              @OA\Property(property="observations", type="string", example="GUIA-VACUNADA"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client departure updated successfully",
     *      ),
     *  )
     * @throws ValidationException
     */
    public function updateDepartureClient (Request $request, $id, $client_id): JsonResponse
    {
        $params = array_merge(
            $request->only(
                'seat',
                'state',
                'observations',
                'room_id',
            ), [
                'id' => $id,
                'client_id' => $client_id
            ]
        );
        $validatedData = Validator::make($params, [
            'id'            => 'required|integer|min:1',
            'client_id'     => 'required|integer|min:1',
            'seat'          => 'string',
            'state'         => 'integer|min:1',
            'observations'  => 'string',
            'room_id'       => 'integer|min:1',
        ])->validate();

        $this->service->updateDepartureClient($id, $client_id, $validatedData);

        return $this->sendResponse([], 'Client updated successfully');
    }

    /**
     * @OA\Post(
     *      path="/api/departures/{id}/add-clients",
     *      tags={"Departures"},
     *      summary="Añade una array clientes a una salida",
     *      security={{"bearer_token":{}}},
     *      description="Añade una clientes a una salida",
     *      operationId="addClientsToDeparture",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de departure",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *          description="Add client to departure",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"clients"},
     *              @OA\Property(
     *                  property="clients",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="client_id", type="integer", example="1"),
     *                      @OA\Property(property="room_type_id", type="integer", example="1"),
     *                      @OA\Property(property="room_id", type="integer", example="1"),
     *                      @OA\Property(property="seat", type="string", example="FINESTRA"),
     *                      @OA\Property(property="state", type="integer", example="3"),
     *                      @OA\Property(property="observations", type="string", example="GUIA-VACUNADA"),
     *                  )
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Departure created successfully",
     *      ),
     *  )
     * @throws ValidationException|ModelNotFoundException
     */
    public function addClients(Request $request, $id): JsonResponse
    {
        $params = array_merge($request->all(), ['id' => $id]);
        $validatedData = Validator::make($params, [
            'id'                        => 'required|integer|min:1',
            'clients.*'                 => 'required|array',
            'clients.*.client_id'       => 'required|integer|min:1',
            'clients.*.room_type_id'    => 'required|integer|min:1',
            'clients.*.room_id'         => 'integer|min:1',
            'clients.*.seat'            => 'string',
            'clients.*.state'           => 'integer|min:1',
            'clients.*.observations'    => 'string',
        ])->validate();

        $this->service->addClients($id, $validatedData['clients']);

        return $this->sendResponse([], 'Clients added successfully');
    }

    ///**
    // * @OA\Post(
    // *      path="/api/departures/{id}/add-client",
    // *      tags={"Departures"},
    // *      summary="Añade un cliente a una salida",
    // *      security={{"bearer_token":{}}},
    // *      description="Añade un cliente a una salida",
    // *      operationId="addClientToDeparture",
    // *      @OA\Parameter(
    // *          name="id",
    // *          in="path",
    // *          description="Id de departure",
    // *          required=true,
    // *          @OA\Schema(type="integer")
    // *      ),
    // *     @OA\RequestBody(
    // *          description="Add client to departure",
    // *          required=true,
    // *          @OA\JsonContent(
    // *              required={"client_id", "room_type_id"},
    // *              @OA\Property(property="client_id", type="integer", example="1"),
    // *              @OA\Property(property="room_type_id", type="integer", example="1"),
    // *              @OA\Property(property="room_id", type="integer", example="1"),
    // *              @OA\Property(property="seat", type="string", example="FINESTRA"),
    // *              @OA\Property(property="state", type="integer", example="3"),
    // *              @OA\Property(property="observations", type="string", example="GUIA-VACUNADA"),
    // *          )
    // *      ),
    // *      @OA\Response(
    // *          response="200",
    // *          description="Departure created successfully",
    // *      ),
    // *  )
    // * @throws ValidationException|ModelNotFoundException
    // */
    //public function addClient(Request $request, $id): JsonResponse
    //{
    //    $params = array_merge($request->only(
    //        'client_id',
    //        'room_type_id',
    //        'room_id',
    //        'seat',
    //        'state',
    //        'observations',
    //    ), ['id' => $id]);
    //    $validatedData = Validator::make($params, [
    //        'id'            => 'required|integer|min:1',
    //        'client_id'     => 'required|integer|min:1',
    //        'room_type_id'  => 'required|integer|min:1',
    //        'room_id'       => 'integer|min:1',
    //        'seat'          => 'string',
    //        'state'         => 'integer|min:1',
    //        'observations'  => 'string',
    //    ])->validate();

    //    $this->service->addClient($id, $validatedData);

    //    return $this->sendResponse([], 'Client added successfully');
    //}

    /**
     * @OA\Delete(
     *      path="/api/departures/{id}/client/{client_id}",
     *      tags={"Departures"},
     *      summary="Elimina un usuario de la salida",
     *      security={{"bearer_token":{}}},
     *      description="Elimina un usuario de la salida",
     *      operationId="removeDeportureClient",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de salida",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="client_id",
     *          in="path",
     *          description="Id del cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client removed successfully",
     *      ),
     *  )
     * @throws ValidationException|ModelNotFoundException
     */
    public function removeClient(Request $request, $id, $client_id): JsonResponse
    {
        Validator::make([
            'id' => $id,
            'client_id' => $client_id,
        ], [
            'id' => 'required|integer|min:1',
            'client_id' => 'required|integer|min:1'
        ])->validate();
        $this->service->removeClient($id, $client_id);
        return $this->sendResponse([], 'Client removed successfully');
    }

    /**
     * @OA\Delete(
     *      path="/api/departures/{id}",
     *      tags={"Departures"},
     *      summary="Elimina una salida",
     *      security={{"bearer_token":{}}},
     *      description="Elimina una salida",
     *      operationId="deleteDeparture",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de salida",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Departure deleted successfully",
     *      ),
     *  )
     * @throws ValidationException|ModelNotFoundException
     */
    public function delete(Request $request, $id): JsonResponse
    {
        Validator::make(['id' => $id], ['id' => 'required'])->validate();
        $this->service->delete($id);
        return $this->sendResponse([], 'Departure deleted successfully');
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @OA\Get(
     *      path="/api/departures/{id}/rooming",
     *      tags={"Departures"},
     *      summary="Retorna los datos de las habitaciones de una salida",
     *      security={{"bearer_token":{}}},
     *      description="Retorna los datos de las habitaciones de una salida",
     *      operationId="departureRoomingList",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de la salida",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Departure rooming info retrieved successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/DepartureRoomingResource")
     *              )
     *          )
     *      )
     *  )
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     */
    public function getDepartureRooming(Request $request, $id): JsonResponse
    {
        return $this->sendResponse(
            new DepartureRoomingResource($this->service->getById($id)),
            'Departure rooming data retrieved successfully'
        );
    }
}
