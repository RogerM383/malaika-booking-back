<?php

namespace App\Http\Controllers;

use App\Exceptions\AppModelNotFoundException;
use App\Exceptions\ClientNotFoundException;
use App\Exceptions\TripNotFoundException;
use App\Http\Controllers\Interfaces\ResourceControllerInterface;
use App\Http\Resources\ClientListCollection;
use App\Http\Resources\ClientListResource;
use App\Http\Resources\TripListCollection;
use App\Http\Resources\TripListResource;
use App\Http\Resources\TripResource;
use App\Services\TripService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TripController extends Controller implements ResourceControllerInterface
{
    use HasPagination;

    private TripService $service;

    public function __construct(TripService $tripService)
    {
        $this->service = $tripService;
    }

    /**
     * @OA\Get(
     *      path="/api/trips",
     *      tags={"Trips"},
     *      summary="Lista de viajes",
     *      security={{"bearer_token":{}}},
     *      description="Lista los viajes",
     *      operationId="tripList",
     *      @OA\Parameter(
     *          name="trip_state",
     *          in="query",
     *          description="Id del estado del viaje",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="client",
     *          in="query",
     *          description="Id de cliente",
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
     *          description="Trip list retrieved successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/TripListResource")
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
            'trip_state'    => 'integer|min:1',
            'client'        => 'integer|min:1',
            'per_page'      => 'integer|min:1',
            'page'          => 'integer|min:1'
        ])->validate();

        if ($this->isPaginated(...$request->only('per_page', 'page'))) {
            $data = new TripListCollection($this->service->all(...$validatedData));
        } else {
            $data = TripListResource::collection($this->service->all(...$validatedData));
        }

        return $this->sendResponse($data, 'Trips retrieved successfully');
    }

    /**
     * @OA\Get(
     *      path="/api/trips/{id}",
     *      tags={"Trips"},
     *      summary="Retorna un viaje por ID",
     *      security={{"bearer_token":{}}},
     *      description="Retorna un viaje",
     *      operationId="getTripById",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de viaje",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Trip retrived successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/TripResource"
     *              )
     *          )
     *      )
     *  )
     * @throws TripNotFoundException|ValidationException
     */
    public function getById(Request $request, $id): JsonResponse
    {
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ])->validate();

        return $this->sendResponse(
            new TripResource($this->service->getById($validatedData)),
            'Trip retrieved successfully'
        );
    }

    public function create(Request $request)
    {
        // TODO: Implement create() method.
    }

    /**
     * @OA\Post(
     *      path="/api/trips/{id}",
     *      tags={"Trips"},
     *      summary="Actualiza los datos del viaje",
     *      security={{"bearer_token":{}}},
     *      description="Actualiza los datos del viaje",
     *      operationId="updateTrip",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de viaje",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *          description="Update trip",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"warehouse_id", "stock"},
     *              @OA\Property(property="title", type="string", example="Tour por Italia"),
     *              @OA\Property(property="description", type="string", example="Tour por Italia"),
     *              @OA\Property(property="commentary", type="string", example="Es un viaje muy chulo, comes pasta y pizza"),
     *              @OA\Property(property="trip_state_id", type="integer", example="1"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Trip updated successfully",
     *      ),
     *  )
     * @throws ValidationException
     * @throws TripNotFoundException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $params = array_merge($request->only($this->service->getFillable()), ['id' => $id]);
        $validatedData = Validator::make($params, [
            'id'            => 'required',
            'title'         => 'string',

            'description'   => 'string',
            //'category',
            'commentary'    => 'string',
            'trip_state_id'      => 'integer|min:1',
        ])->validate();

        return $this->sendResponse(
            new TripResource($this->service->update($id, $validatedData)),
            'Trip updated successfully'
        );
    }
}
