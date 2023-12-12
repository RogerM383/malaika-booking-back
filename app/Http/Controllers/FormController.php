<?php

namespace App\Http\Controllers;

use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\DepartureTypeRoomCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\Trip\TripFormResource;
use App\Mail\NewInscriptionClient;
use App\Mail\NewInscriptionEsperaClient;
use App\Services\ClientService;
use App\Services\ClientTypeService;
use App\Services\DepartureService;
use App\Services\RoomService;
use App\Services\RoomTypeService;
use App\Services\TripService;
use App\Traits\HandleDates;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use function PHPUnit\Framework\logicalOr;

class FormController extends Controller
{
    use HandleDates;

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
     * @throws DepartureTypeRoomCapacityExceededException
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

            // Creamos clientes
            $clients = [];
            foreach ($request['clients'] as $client) {
                $data = [
                    'name'      => $client['name'],
                    'surname'   => $client['surname'],
                    'dni'       => $client['dni'],
                    'MNAC'      => $client['MNAC'] ?? null
                ];
                if (!empty($client['MNAC'])) {
                    $data['client_type_id'] = 2;
                }
                $clients[] = $this->clientService->create($data);
            }

            $clientsCollection = collect($clients);

            // Creamos las habitaciones
            foreach ($validatedData['rooms'] as $roomData) {
                $roomTypeId     = $roomData['room_type_id'];
                $roomQuantity   = $roomData['quantity'];
                $roomType       = $this->roomTypeService->getById($roomTypeId);
                $capacity       = $roomType->capacity;
                $name           = $roomType->name;

                for ($i = 1; $i <= $roomQuantity; $i++) {

                    $assignedClients = $clientsCollection->splice(0, $capacity);

                    // Assignamos todos los clientes a la salida
                    $departure->clients()->attach(
                        $assignedClients->mapWithKeys(function ($client, $key) use ($roomTypeId) {
                            return [$client['id'] => ['room_type_id' => $roomTypeId, 'state' => 6]];
                        })
                    );
                }
            }

            Mail::to($validatedData['contact_email'])
                ->bcc('kirian@fruntera.com')
                //->bcc('aayats@malaikaviatges.com')
                ->send(new NewInscriptionEsperaClient());

            return $this->sendError(
                'En espera'
            );
            //throw new DeparturePaxCapacityExceededException();
        }

        foreach ($validatedData['rooms'] as $room) {
            if (!$departure->hasEnoughRooms($room['room_type_id'], $room['quantity'])) {

                // Creamos clientes
                $clients = [];
                foreach ($request['clients'] as $client) {
                    $data = [
                        'name'      => $client['name'],
                        'surname'   => $client['surname'],
                        'dni'       => $client['dni'],
                        'MNAC'      => $client['MNAC'] ?? null
                    ];
                    if (!empty($client['MNAC'])) {
                        $data['client_type_id'] = 2;
                    }
                    $clients[] = $this->clientService->create($data);
                }

                $clientsCollection = collect($clients);


                $clientsCollection = collect($clients);

                // Creamos las habitaciones
                foreach ($validatedData['rooms'] as $roomData) {
                    $roomTypeId     = $roomData['room_type_id'];
                    $roomQuantity   = $roomData['quantity'];
                    $roomType       = $this->roomTypeService->getById($roomTypeId);
                    $capacity       = $roomType->capacity;
                    $name           = $roomType->name;

                    for ($i = 1; $i <= $roomQuantity; $i++) {

                        $assignedClients = $clientsCollection->splice(0, $capacity);

                        // Assignamos todos los clientes a la salida
                        $departure->clients()->attach(
                            $assignedClients->mapWithKeys(function ($client, $key) use ($roomTypeId) {
                                return [$client['id'] => ['room_type_id' => $roomTypeId, 'state' => 6]];
                            })
                        );
                    }
                }

                Mail::to($validatedData['contact_email'])
                    ->bcc('kirian@fruntera.com')
                    //->bcc('aayats@malaikaviatges.com')
                    ->send(new NewInscriptionEsperaClient());

                return $this->sendError(
                    'En espera'
                );
                //throw new DepartureTypeRoomCapacityExceededException();
            }
        }

        // Creamos clientes
        $clients = [];
        foreach ($request['clients'] as $client) {
            $data = [
                'name'      => $client['name'],
                'surname'   => $client['surname'],
                'dni'       => $client['dni'],
                'MNAC'      => $client['MNAC'] ?? null
            ];
            if (!empty($client['MNAC'])) {
                $data['client_type_id'] = 2;
            }
            $clients[] = $this->clientService->create($data);
        }

        $clientsCollection = collect($clients);

        $mailRooms = [];

        // Creamos las habitaciones
        foreach ($validatedData['rooms'] as $roomData) {
            $roomTypeId     = $roomData['room_type_id'];
            $roomQuantity   = $roomData['quantity'];
            $roomType       = $this->roomTypeService->getById($roomTypeId);
            $capacity       = $roomType->capacity;
            $name           = $roomType->name;

            $prettyNames    = [
                'Dui'       => 'Doble individual',
                'Doble'     => 'Doble',
                'Twin'      => 'Doble amb dos llits',
                'Triple'    => 'Triple'
            ];

            $mailRooms[] = [
                'quantity' => $roomQuantity,
                'name' => $prettyNames[$name]
            ];

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

                $departure->roomTypes()->newPivotQuery()->where('room_type_id', $roomTypeId)->increment('quantity');
            }
        }

        $count = $departure->rooms->groupBy('created_at')->count();
        $number = $departure->expedient . str_pad($count, 3, '0', STR_PAD_LEFT);

        $data = [
            'title'     => $departure->trip->title,
            'booking_price' => $departure->booking_price,
            'clients'   => $validatedData['clients'],
            'rooms'     => $mailRooms,
            'contact'   => [
                'name'      => $validatedData['contact_name'],
                'surname'   => $validatedData['contact_surname'],
                'phone'     => $validatedData['contact_phone'],
                'email'     => $validatedData['contact_email'],
            ],
            'pdf' => $departure->trip->pdf,
            'number' => $number,
            'dates'  => $this->getPeriod($departure->start, $departure->final)
        ];

        Mail::to($validatedData['contact_email'])
            //->bcc('kirian@fruntera.com')
            ->bcc(['aayats@malaikaviatges.com', 'kirian@fruntera.com', 'roger@fruntera.com'])
            ->send(new NewInscriptionClient($data));

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
