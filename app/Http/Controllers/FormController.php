<?php

namespace App\Http\Controllers;

use App\Exceptions\DepartureNotFoundException;
use App\Services\DepartureService;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormController extends Controller
{
    private DepartureService $departureService;
    private RoomService $roomService;

    public function __construct(
        DepartureService $departureService,
        RoomService $roomService)
    {
        $this->departureService = $departureService;
        $this->roomService = $roomService;
    }

    /**
     * @OA\Post(
     *      path="/api/forms/process",
     *      tags={"Forms"},
     *      summary="Procesa un formulario",
     *      security={{"bearer_token":{}}},
     *      description="Procesa un formulario",
     *      operationId="prcessForm",
     *      @OA\Response(
     *          response="200",
     *          description="Form processed succesfully",
     *      ),
     *     @OA\RequestBody(
     *          description="Process form",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"departure_id"},
     *              @OA\Property(property="departure_id", type="integer", example="1"),
     *
     *              @OA\Property(property="name", type="string", example="Selene"),
     *              @OA\Property(property="surname", type="string", example="Selenita"),
     *              @OA\Property(property="dni", type="string", example="47854123X"),
     *              @OA\Property(property="MNAC", type="string", example="4545454545"),
     *
     *              @OA\Property(property="room_type_id", type="integer", example="1"),
     *
     *              @OA\Property(property="contact_name", type="string", example="Selene"),
     *              @OA\Property(property="contact_surname", type="string", example="Selenita"),
     *              @OA\Property(property="contact_phone", type="string", example="6945798"),
     *              @OA\Property(property="contact_email", type="string", example="selene@gmail.com"),
     *          )
     *      )
     *  )
     * @throws ValidationException
     * @throws DepartureNotFoundException
     */
    public function process(Request $request): JsonResponse
    {
        Log::debug('ME cago en to lo que se menea');

        /*$validatedData = Validator::make($request->all(), [
            'departure_id'      => 'required|integer|min:1',

            'room_type_id'      => 'required|integer|min:1',

            'name'              => 'required|string',
            'surname'           => 'string',
            'dni'               => 'string',
            'MNAC'              => 'string',

            'contact_name'      => 'required|string',
            'contact_surname'   => 'string',
            'contact_phone'     => 'string',
            'contact_email'     => 'string|email',
        ])->validate();

        Log::debug('VALIDATED DATA YEPE YIPA YEY MOTHEDRFUCKERS');
        Log::debug(json_encode($validatedData));

        $v = $this->departureService->addClient(
            $validatedData['departure_id'],
            $validatedData['client_id'],
            $validatedData['room_type_id']
        );

        Log::debug(json_encode($v));

        $r = $this->departureService->addRoom(
            $validatedData['id'],
            $validatedData['client_id'],
            $validatedData['room_type_id'],
            $validatedData['observations'],
        );

        Log::debug(json_encode($r));

        $lowestNum = $this->roomService->getNextRoomNumber($validatedData['departure_id']);

        return $this->sendResponse(
            ['message' => 'OK', 'num' => $lowestNum],
            'Form processed successfully'
        );*/
    }
}
