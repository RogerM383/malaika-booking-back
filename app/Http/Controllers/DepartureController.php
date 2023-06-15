<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Interfaces\ResourceControllerInterface;
use App\Http\Resources\DepartureCollection;
use App\Http\Resources\DepartureResource;
use App\Http\Resources\TripListCollection;
use App\Http\Resources\TripListResource;
use App\Services\DepartureService;
use App\Services\TripService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DepartureController extends Controller implements ResourceControllerInterface
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
        $validatedData = Validator::make($request->all(), [
            'trip_id'       => 'integer|min:1',
            'per_page'      => 'integer|min:1',
            'page'          => 'integer|min:1'
        ])->validate();

        if ($this->isPaginated(...$request->only('per_page', 'page'))) {
            $data = new DepartureCollection($this->service->all(...$validatedData));
        } else {
            $data = DepartureResource::collection($this->service->all(...$validatedData));
        }

        return $this->sendResponse($data, 'Departures retrieved successfully');
    }

    public function getById(Request $request, $id)
    {
        // TODO: Implement getById() method.
    }

    public function create(Request $request)
    {
        // TODO: Implement create() method.
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement update() method.
    }
}
