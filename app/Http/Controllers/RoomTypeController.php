<?php

namespace App\Http\Controllers;

use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\Departure\DepartureExportResource;
use App\Http\Resources\Departure\DepartureResource;
use App\Http\Resources\Language\LanguageResource;
use App\Http\Resources\RoomType\RoomTypeResource;
use App\Services\LanguageService;
use App\Services\RoomTypeService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RoomTypeController extends Controller
{
    use HasPagination;

    private RoomTypeService $service;

    public function __construct(RoomTypeService $roomTypeService)
    {
        $this->service = $roomTypeService;
    }

    /**
     * @OA\Get(
     *      path="/api/room-types",
     *      tags={"RoomTypes"},
     *      summary="Lista los tipos de habitacion",
     *      security={{"bearer_token":{}}},
     *      description="Lista los tipos de habitacion",
     *      operationId="roomTypeList",
     *      @OA\Response(
     *          response="200",
     *          description="RoomType list retrieved successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/RoomTypeResource")
     *              )
     *          )
     *      )
     *  )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $data = RoomTypeResource::collection($this->service->get());
        return $this->sendResponse($data, 'Room type retrieved successfully');
    }

    /**
     * @OA\Get(
     *      path="/api/room-types/{id}",
     *      tags={"RoomTypes"},
     *      summary="Retorna un tipo de habitacion",
     *      security={{"bearer_token":{}}},
     *      description="Retorna un tipo de habitacion",
     *      operationId="roomTypeData",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de tipo de habitacion",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="RoomType retrieved successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/RoomTypeResource")
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
            new RoomTypeResource($this->service->getById($id)),
            'Room type retrieved successfully'
        );
    }
}
