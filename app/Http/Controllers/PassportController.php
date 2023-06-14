<?php

namespace App\Http\Controllers;

use App\Exceptions\AppModelNotFoundException;
use App\Exceptions\ClientNotFoundException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\PassportNotFoundException;
use App\Http\Controllers\Interfaces\ResourceControllerInterface;
use App\Http\Resources\ClientResource;
use App\Http\Resources\PassportDetailResource;
use App\Http\Resources\PassportResource;
use App\Services\PassportService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PassportController extends Controller implements ResourceControllerInterface
{
    use HasPagination;

    private PassportService $service;

    public function __construct(PassportService $passportService)
    {
        $this->service = $passportService;
    }

    /**
     * @OA\Post(
     *      path="/api/passports",
     *      tags={"Passports"},
     *      summary="Crea un nuevo pasaporte",
     *      security={{"bearer_token":{}}},
     *      description="Crea un nuevo pasaporte",
     *      operationId="createPassport",
     *      @OA\Response(
     *          response="200",
     *          description="Passport created successfully",
     *      ),
     *     @OA\RequestBody(
     *          description="Create passport",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"number_passport", "client_id"},
     *              @OA\Property(property="client_id", type="integer", description="Client ID", example="1"),
     *              @OA\Property(property="number_passport", type="string", description="Passport number", example="4567845454WW"),
     *              @OA\Property(property="birth", type="string", description="Passport owner bith date", example="1975/12/01"),
     *              @OA\Property(property="issue", type="string", description="Passport issue", example="2021/12/01"),
     *              @OA\Property(property="exp", type="string", description="Passport expiration date", example="2026/12/01"),
     *              @OA\Property(property="nationality", type="string", description="Passport owner nacinality", example="Spanish"),
     *          )
     *      )
     *  )
     * @throws ValidationException|ClientNotFoundException
     */
    public function create(Request $request): JsonResponse
    {
        $validatedData = Validator::make(array_merge($request->only($this->service->getFillable()), ['client_id' => $request->input('client_id')]), [
            'client_id'         => 'required|integer',
            'number_passport'   => 'required|string',
            'birth'             => 'string',
            'issue'             => 'string',
            'exp'               => 'string',
            'nationality'       => 'string',
        ])->validate();

        return $this->sendResponse(
            new PassportResource($this->service->create($validatedData)),
            'Passport created successfully'
        );
    }

    /**
     * @OA\Post(
     *      path="/api/passports/{id}",
     *      tags={"Passports"},
     *      summary="Actualiza los datos del pasaporte",
     *      security={{"bearer_token":{}}},
     *      description="Actualiza los datos del pasaporte",
     *      operationId="updatePassport",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id del pasaporte",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\RequestBody(
     *          description="Update passport",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"number_passport"},
     *              @OA\Property(property="number_passport", type="string", description="Passport number", example="4567845454WW"),
     *              @OA\Property(property="birth", type="string", description="Passport owner bith date", example="1975/12/01"),
     *              @OA\Property(property="issue", type="string", description="Passport issue", example="2021/12/01"),
     *              @OA\Property(property="exp", type="string", description="Passport expiration date", example="2026/12/01"),
     *              @OA\Property(property="nationality", type="string", description="Passport owner nacinality", example="Spanish"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Passport updated successfully",
     *      ),
     *  )
     * @throws ValidationException|PassportNotFoundException|ModelNotFoundException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $params = array_merge($request->only($this->service->getFillable()), ['id' => $id]);
        Log::debug(json_encode($params));
        $validatedData = Validator::make($params, [
            'id'                => 'required',
            'number_passport'   => 'required|string',
            'birth'             => 'string',
            'issue'             => 'string',
            'exp'               => 'string',
            'nationality'       => 'string',
        ])->validate();

        return $this->sendResponse(
            new PassportResource($this->service->update($id, $validatedData)),
            'Passport updated successfully'
        );
    }

    /**
     * @OA\Get(
     *      path="/api/passports/{id}",
     *      tags={"Passports"},
     *      summary="Retorna un pasaporte por ID",
     *      security={{"bearer_token":{}}},
     *      description="Retorna un pasaporte",
     *      operationId="getPassportById",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de pasaporte",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Passport retrived successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/PassportDetailResource"
     *              )
     *          )
     *      )
     *  )
     * @throws ValidationException|AppModelNotFoundException
     */
    public function getById(Request $request, $id): JsonResponse
    {
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ])->validate();

        return $this->sendResponse(
            new PassportDetailResource($this->service->getById($validatedData['id'])),
            'Passport retrieved successfully'
        );
    }

    public function get(Request $request)
    {
        // TODO: Implement get() method.
    }
}
