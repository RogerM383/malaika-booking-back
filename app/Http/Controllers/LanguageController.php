<?php

namespace App\Http\Controllers;

use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\Departure\DepartureExportResource;
use App\Http\Resources\Departure\DepartureResource;
use App\Http\Resources\Language\LanguageResource;
use App\Services\LanguageService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LanguageController extends Controller
{
    use HasPagination;

    private LanguageService $service;

    public function __construct(LanguageService $languageService)
    {
        $this->service = $languageService;
    }

    /**
     * @OA\Get(
     *      path="/api/languages",
     *      tags={"Languages"},
     *      summary="Lista los lenguajes",
     *      security={{"bearer_token":{}}},
     *      description="Lista los lenguajes",
     *      operationId="languageList",
     *      @OA\Response(
     *          response="200",
     *          description="Departure list retrieved successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/LanguageResource")
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
        $data = LanguageResource::collection($this->service->get());
        return $this->sendResponse($data, 'Languages retrieved successfully');
    }

    /**
     * @OA\Get(
     *      path="/api/languages/{id}",
     *      tags={"Languages"},
     *      summary="Retorna un lenguaje",
     *      security={{"bearer_token":{}}},
     *      description="Retorna un lenguaje",
     *      operationId="languageData",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de lenguaje",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Language retrieved successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/LanguageResource")
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
            new LanguageResource($this->service->getById($id)),
            'Language retrieved successfully'
        );
    }
}
