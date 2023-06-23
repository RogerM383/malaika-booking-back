<?php

namespace App\Http\Controllers;

use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Services\ClientService;
use App\Services\ClientTypeService;
use App\Services\DepartureService;
use App\Services\RoomService;
use App\Services\RoomTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function __construct(
        ClientService $clientService,
        DepartureService $departureService,
        RoomService $roomService,
        ClientTypeService $clientTypeService,
        RoomTypeService $roomTypeService
    )
    {
        $this->clientService        = $clientService;
        $this->departureService     = $departureService;
        $this->roomService          = $roomService;
        $this->clientTypeService    = $clientTypeService;
        $this->roomTypeService      = $roomTypeService;
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

        $departureId = $validatedData['departure_id'];

        // --- Check si tenemos suficiente espacio ---------------------------------------------------------------------
        // Get available slots
        $availableSlots = $this->departureService->getAvailableSlots($departureId);

        // Si no hay suficientes slots salta error
        if ($availableSlots < count($validatedData['clients'])) {
            throw new DeparturePaxCapacityExceededException();
        }

        // --- Check si hay suficientes habitacions del tipo requerido por el usuario ----------------------------------
        // Get room types


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
            $clients[] = $this->clientService->make($data);
        }

        // Creamos las habitaciones
        $rooms = [];
        foreach ($validatedData['rooms'] as $room) {
            $roomTypeId = $room['room_type_id'];
            $roomQuantity = $room['quantity'];
            // Creamos la array de habitaciones
            for ($i = 1; $i < $roomQuantity; $i++) {
                $rooms[] = $this->roomService->make([
                    'room_type_id'  => $roomTypeId,
                    'room_number'   => $this->roomService->getNextRoomNumber($departureId),
                ]);
            }
            Log::debug(json_encode($room));
            $capacity = $this->roomTypeService->getById($roomTypeId)->capacity;
            Log::debug($capacity);
            for ($i = 1; $i < $capacity; $i++) {

            }
        }

/*


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

            $client = $this->clientService->create($data);

            // 1.- Crea un cliente apara cada item de la rray de clientes
            // 2.- Segun los tipos de

        }



        // ---------------------------------------------------------------------

        $r = $this->departureService->addRoom(
            $validatedData['departure_id'],
            $client->id,
            $validatedData['room_type_id'],
            null
            //$validatedData['observations'],
        );

        $v = $this->departureService->addClient(
            $validatedData['departure_id'],
            $client->id,
            $validatedData['room_type_id']
        );

        Log::debug(json_encode($v));



        Log::debug(json_encode($r));
*/
        $lowestNum = $this->roomService->getNextRoomNumber($validatedData['departure_id']);

        return $this->sendResponse(
            ['message' => 'OK', 'num' => $lowestNum],
            'Form processed successfully'
        );
    }
}
