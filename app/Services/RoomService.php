<?php

namespace App\Services;

use App\Exceptions\AppModelNotFoundException;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class RoomService extends ResourceService
{
    protected $model;

    //private DepartureService $departureService;

    /**
     * @param Room $model
     */
    #[Pure] public function __construct(Room $model/*, DepartureService $departureService*/)
    {
        parent::__construct($model);
        //$this->departureService = $departureService;
    }

    /**
     * @param $departure
     * @param $client_id
     * @param $room_type_id
     * @param $observations
     * @return Room
     */
    public function createInDeparture($departure, $client_id, $room_type_id, $observations): Room
    {
        // $departure = $this->departureService->getById($departure_id);
        $room = $departure->rooms()->create([
            'room_type_id'  => $room_type_id,
            'room_number'   => $this->getNextRoomNumber($departure->id), // Calcular de alguna manerda el numero de habitacion / ultimo o hueco
            'observations'  => $observations
        ]);
        $room->clients()->attach($client_id); // TODO: asegurarme de que no necesitan observaciones
        return $room;
    }

    public function getNextRoomNumber($departure_id)
    {
        $lowestAvailableRoomNumber = DB::table('rooms')
            ->selectRaw('MIN(room_number + 1) AS lowest_available')
            ->where('departure_id', $departure_id)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('rooms as t2')
                    ->whereRaw('rooms.room_number + 1 = t2.room_number');
            })
            ->pluck('lowest_available')
            ->first();
        Log::debug($lowestAvailableRoomNumber);
        return $lowestAvailableRoomNumber;
    }
}
