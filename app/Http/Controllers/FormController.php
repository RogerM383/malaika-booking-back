<?php

namespace App\Http\Controllers;

use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\Trip\TripFormResource;
use App\Services\ClientService;
use App\Services\ClientTypeService;
use App\Services\DepartureService;
use App\Services\RoomService;
use App\Services\RoomTypeService;
use App\Services\TripService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    private ClientService $clientService;
    private ClientTypeService $clientTypeService;
    private DepartureService $departureService;
    private RoomService $roomService;
    private RoomTypeService $roomTypeService;
    private TripService $tripService;

    public function __construct(
        ClientService $clientService,
        DepartureService $departureService,
        RoomService $roomService,
        ClientTypeService $clientTypeService,
        RoomTypeService $roomTypeService,
        TripService $tripService,
    )
    {
        $this->clientService        = $clientService;
        $this->departureService     = $departureService;
        $this->roomService          = $roomService;
        $this->clientTypeService    = $clientTypeService;
        $this->roomTypeService      = $roomTypeService;
        $this->tripService          = $tripService;
    }

    /**
     * @OA\Post(
     *      path="/api/forms/process",
     *      tags={"Forms"},
     *      summary="Procesa un formulario",
     *      security={{"bearer_token":{}}},
     *      description="Procesa un formulario",
     *      operationId="processForm",
     *      @OA\RequestBody(
     *          description="Process form",
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="clients",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="name", type="string", example="Selene"),
     *                      @OA\Property(property="surname", type="string", example="Selenita"),
     *                      @OA\Property(property="dni", type="string", example="47854123X"),
     *                      @OA\Property(property="MNAC", type="string", example="4545454545"),
     *                  )
     *              ),
     *              @OA\Property(property="departure_id", type="integer", example="1"),
     *              @OA\Property(
     *                  property="rooms",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="room_type_id", type="integer", example="1"),
     *                      @OA\Property(property="quantity", type="integer", example="1"),
     *                  )
     *              ),
     *              @OA\Property(property="contact_name", type="string", example="Selene"),
     *              @OA\Property(property="contact_surname", type="string", example="Selenita"),
     *              @OA\Property(property="contact_phone", type="string", example="6945798"),
     *              @OA\Property(property="contact_email", type="string", example="selene@gmail.com"),
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Form processed successfully",
     *      ),
     *      @OA\Response(
     *          response="400",
     *          description="Bad request",
     *      )
     * )
     *
     * @throws ValidationException|ModelNotFoundException
     * @throws DeparturePaxCapacityExceededException
     */
    public function process(Request $request): JsonResponse
    {
        $validatedData = Validator::make($request->all(), [
            'departure_id'          => 'required|integer|min:1',
            'clients'               => 'required|array',
            'clients.*.name'        => 'required|string',
            'clients.*.surname'     => 'string',
            'clients.*.dni'         => 'string',
            'clients.*.MNAC'        => 'string',
            'rooms'                 => 'required|array',
            'rooms.*.room_type_id'  => 'required|integer|min:1',
            'rooms.*.quantity'      => 'required|integer|min:1',
            'contact_name'          => 'required|string',
            'contact_surname'       => 'string',
            'contact_phone'         => 'string',
            'contact_email'         => 'string|email',
        ])->validate();

        $departureId    = $validatedData['departure_id'];
        $clientsCount   = count($validatedData['clients']);
        $departure      = $this->departureService->getById($departureId);

        if (!$departure->hasEnoughSpace($clientsCount)) {
            throw new DeparturePaxCapacityExceededException();
        }

        // Get departure room types
        $departureRoomTypes = $departure->roomTypes->mapWithKeys(function ($item, int $key) {
                return [$item['id'] => $item['pivot']['quantity']];
            });
        // Get departure assigned room types count
        $assignedRoomTypes = $departure->assignedRoomsCount()->mapWithKeys(function ($room, $key) {
            return [$room['room_type_id'] => $room['quantity']];
        });
        // Get departure available room types
        $availableRoomTypes = [];
        foreach ($assignedRoomTypes as $key => $value) {
            if (is_null($departureRoomTypes[$key])) {
                $availableRoomTypes[$key] = null;
            } else {
                $availableRoomTypes[$key] = $departureRoomTypes[$key] - $value;
            }
        }
        // Check si hay suficientes habitaciones de cada tipos solicitado
        $requestedRooms = collect($validatedData['rooms'])->mapWithKeys(function ($room, $key) {
            return [$room['room_type_id'] => $room['quantity']];
        });

        /*Log::debug('DEPARTURE ROOM TYPES');
        Log::debug(json_encode($departureRoomTypes));
        Log::debug('ASSIGNED ROOMS TYPES');
        Log::debug(json_encode($assignedRoomTypes));
        Log::debug('AVAILABLE ROOM TYPES');
        Log::debug(json_encode($availableRoomTypes));
        Log::debug('REQUESTED ROOM TYPES');
        Log::debug(json_encode($requestedRooms));*/

        $enoughRooms = $requestedRooms->every(function (int $value, int $key) use ($availableRoomTypes) {
            return !isset($availableRoomTypes[$key]) || $availableRoomTypes[$key] >= $value;
        });

        if (!$enoughRooms) {
            // TODO: Cambiar esto por un throw new RequiredRoomType o alguna historia asi
            Log::error('Not enough rooms of required types');
        }

        // Creamos clientes
        $clients = [];
        foreach ($request['clients'] as $client) {
            $data = [
                'name'      => $client['name'],
                'surname'   => $client['surname'],
                'dni'       => $client['dni'],
                'MNAC'      => $client['MNAC']
            ];
            if (!empty($request->MNAC)) {
                $data['client_type_id'] = 2;
            }
            $clients[] = $this->clientService->create($data);
        }
        $clientsCollection = collect($clients);

        // Creamos las habitaciones
        foreach ($validatedData['rooms'] as $roomData) {
            $roomTypeId     = $roomData['room_type_id'];
            $roomQuantity   = $roomData['quantity'];
            $capacity       = $this->roomTypeService->getById($roomTypeId)->capacity;

            for ($i = 1; $i <= $roomQuantity; $i++) {
                $room = $this->roomService->create([
                    'room_type_id'  => $roomTypeId,
                    'room_number'   => $this->roomService->getNextRoomNumber($departureId),
                    'departure_id'  => $departureId
                ]);

                $assignedClients = $clientsCollection->splice(0, $capacity);

                $room->clients()->sync($assignedClients->pluck('id'));

                // Assignamos todos los clientes a la salida
                $departure->clients()->attach(
                    $assignedClients->mapWithKeys(function ($client, $key) use ($roomTypeId) {
                        return [$client['id'] => ['room_type_id' => $roomTypeId]];
                    })
                );
            }
        }

        return $this->sendResponse(
            ['message' => 'OK'],
            'Form processed successfully'
        );
    }

    /**
     * @OA\Get(
     *      path="/api/forms/trips/{slug}",
     *      tags={"Forms"},
     *      summary="Retorna los datos del formulario de un viaje por slug",
     *      security={{"bearer_token":{}}},
     *      description="Retorna los datos del formulario de un viaje por slug",
     *      operationId="getFormTripBySlug",
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
    public function getFormTripBySlug(Request $request, $slug): JsonResponse
    {
        $validatedData = Validator::make(['slug' => $slug], [
            'slug' => 'required|string',
        ])->validate();

        return $this->sendResponse(
            new TripFormResource($this->tripService->getBySlug($validatedData['slug'])),
            'Trip data retrieved successfully'
        );
    }
}
