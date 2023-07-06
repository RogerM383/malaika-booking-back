<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientType\ClientTypeListResource;
use App\Services\ClientTypeService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientTypeController extends Controller
{
    use HasPagination;

    private ClientTypeService $service;

    public function __construct(ClientTypeService $clietnTypeService)
    {
        $this->service = $clietnTypeService;
    }

    /**
     * @OA\Get(
     *      path="/api/client-types",
     *      tags={"ClientTypes"},
     *      summary="Lista los tipos de cliente",
     *      security={{"bearer_token":{}}},
     *      description="Lista los tipos de cliente",
     *      operationId="clientTypesList",
     *      @OA\Response(
     *          response="200",
     *          description="Client types list retrieved successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/ClientTypeListResource")
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
        $data = ClientTypeListResource::collection($this->service->get());
        return $this->sendResponse($data,'Client types list retrieved successfully');
    }
}
