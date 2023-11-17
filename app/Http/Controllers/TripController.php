<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\Departure\DepartureResource;
use App\Http\Resources\Trip\TripFormResource;
use App\Http\Resources\Trip\TripListCollection;
use App\Http\Resources\Trip\TripListResource;
use App\Http\Resources\Trip\TripResource;
use App\Services\TripService;
use App\Traits\HasPagination;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TripController extends Controller
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
            $data = new TripListCollection($this->service->get(...$validatedData));
        } else {
            $data = TripListResource::collection($this->service->get(...$validatedData));
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
     * @throws ValidationException|ModelNotFoundException
     */
    public function getById(Request $request, $id): JsonResponse
    {
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ])->validate();

        return $this->sendResponse(
            new TripResource($this->service->getById($validatedData['id'])),
            'Trip retrieved successfully'
        );
    }

    /**
     * @OA\Get(
     *      path="/api/trips/form/{slug}",
     *      tags={"Trips"},
     *      summary="Retorna los datos del formulario de un viaje por slug",
     *      security={{"bearer_token":{}}},
     *      description="Retorna los datos del formulario de un viaje por slug",
     *      operationId="getTripBySlug",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug de viaje",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Trip data retrived successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/TripFormResource"
     *              )
     *          )
     *      )
     *  )
     * @throws ValidationException|ModelNotFoundException
     */
    public function getBySlug(Request $request, $slug): JsonResponse
    {
        $validatedData = Validator::make(['slug' => $slug], [
            'slug' => 'required|string',
        ])->validate();

        return $this->sendResponse(
            new TripFormResource($this->service->getBySlug($validatedData['slug'])),
            'Trip data retrieved successfully'
        );
    }


    //*         @OA\MediaType(
    //*             mediaType="multipart/form-data",
    //*             @OA\Schema(
    //*                 type="object",
    //*                 required={"image"},
    //*                 @OA\Property(
    //*                     property="image",
    //*                     description="Imagen del viaje",
    //*                     type="string",
    //*                     format="binary",
    //*                 ),
    //*             ),
    //*         ),

    /**
     * @OA\Post(
     *     path="/api/trips",
     *     tags={"Trips"},
     *     summary="Crea un nuevo viaje",
     *     security={{"bearer_token":{}}},
     *     description="Crea un nuevo viaje. Asegúrate de que todos los campos obligatorios estén incluidos en la solicitud JSON. También puedes cargar una imagen adjunta en el formulario de carga de archivos.",
     *     operationId="createTrip",
     *     @OA\RequestBody(
     *         description="Datos del viaje",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "commentary", "trip_state_id"},
     *             @OA\Property(property="title", type="string", example="Tour por Italia"),
     *             @OA\Property(property="description", type="string", example="Tour por Italia"),
     *             @OA\Property(property="commentary", type="string", example="Es un viaje muy chulo, comes pasta y pizza"),
     *             @OA\Property(property="trip_state_id", type="integer", example="1"),
     *         ),

     *     ),

     *     @OA\Response(
     *         response="200",
     *         description="Viaje creado con éxito",
     *     ),
     * )
     */

    public function create(Request $request)
    {
        $image = $request->file('image');

        $validatedData = Validator::make([...$request->all(), 'image' => $image], [
            'title'             => 'nullable|string',
            'description'       => 'string',
            'commentary'        => 'nullable|string',
            'trip_state_id'     => 'integer',
            'image'             => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'before_open_text'  => 'nullable|string',
            'after_close_text'  => 'nullable|string',
        ])->validate();
        return $this->sendResponse(
            new TripResource($this->service->create($validatedData)),
            'Trip created successfully'
        );
    }

    /**
     * @OA\Put(
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
     * @throws ValidationException|ModelNotFoundException
     */
    public function update(Request $request, $id): JsonResponse
    {
        $params = array_merge($request->only($this->service->getFillable()), ['id' => $id]);
        $validatedData = Validator::make($params, [
            'id'                => 'required',
            'title'             => 'string',

            'description'       => 'nullable|string',
            //'category',
            'commentary'        => 'nullable|string',
            'trip_state_id'     => 'integer|min:1',
            'image'             => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'before_open_text'  => 'nullable|string',
            'after_close_text'  => 'nullable|string',
        ])->validate();

        Log::debug($validatedData);

        return $this->sendResponse(
            new TripResource($this->service->update($id, $validatedData)),
            'Trip updated successfully'
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/trips/{id}",
     *      tags={"Trips"},
     *      summary="Elimina un viaje",
     *      security={{"bearer_token":{}}},
     *      description="Elimina un viaje",
     *      operationId="deleteTrip",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de viaje",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Trip deleted successfully",
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
