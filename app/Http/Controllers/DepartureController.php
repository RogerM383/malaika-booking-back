<?php

namespace App\Http\Controllers;

use App\Http\Resources\Departure\DepartureCollection;
use App\Http\Resources\Departure\DepartureDetailsResource;
use App\Http\Resources\Departure\DepartureResource;
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
     *          description="Id del viaje",
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
        Log::debug('mecago en dios');
        $validatedData = Validator::make($request->all(), [
            'trip_id'       => 'integer|min:1',
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
     * @throws DepartureNotFoundException
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
     * @throws ValidationException|TripNotFoundException
     */
    public function create(Request $request): JsonResponse
    {
        //$params = array_merge($request->only($this->service->getFillable());
        $validatedData = Validator::make($request->all(), [
            'trip_id'               => 'required|integer',
            'start'                 => 'required|string',
            'final'                 => 'required|string',
            'price'                 => 'required|numeric',
            'pax_capacity'         => 'required|integer',
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
     * @throws DepartureNotFoundException
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
            'pax_capacity'         => 'integer',
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
            new DepartureDetailsResource($this->service->update($id, $validatedData)),
            'Departure updated successfully'
        );
    }

    /**
     * @OA\Post(
     *      path="/api/departures/{id}/add-client",
     *      tags={"Departures"},
     *      summary="Añade un cliente a una salida",
     *      security={{"bearer_token":{}}},
     *      description="Añade un cliente a una salida",
     *      operationId="addClientToDeparture",
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
     *              required={"client_id"},
     *              @OA\Property(property="client_id", type="integer", example="1"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Departure created successfully",
     *      ),
     *  )
     * @throws ValidationException|TripNotFoundException
     */
    public function addClient(Request $request, $id)
    {
        $params = array_merge($request->only('client_id'), ['id' => $id]);
        $validatedData = Validator::make($params, [
            'id'        => 'required|integer',
            'client_id' => 'required@integer',
        ])->validate();

        $this->service->addClient(...$validatedData);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @OA\Get(
     *      path="/api/departures/{id}/rooming",
     *      tags={"Departures"},
     *      summary="Lista las salidas",
     *      security={{"bearer_token":{}}},
     *      description="Lista las salidas",
     *      operationId="departureRoomingList",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id del viaje",
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
     * @param $id
     * @return JsonResponse
     * @throws DepartureNotFoundException
     */
    /*public function getDepartureRooming(Request $request, $id): JsonResponse
    {
        return $this->sendResponse(
            $this->service->getDepartureRoomingData($id),
            'Departure updated successfully'
        );
    }*/
}
