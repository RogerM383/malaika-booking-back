<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
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
     * @param null $departure_id
     * @param null $room_type_id
     * @param null $per_page
     * @param null $page
     * @return Collection|LengthAwarePaginator
     */
    public function get(
        $departure_id = null,
        $room_type_id = null,
        $per_page = null,
        $page = null,
    ): Collection|LengthAwarePaginator
    {
        $query = $this->model::query();

        if ($departure_id) {
            $this->addDepartureIdFilter($query, $departure_id);
        }

        if ($room_type_id) {
            $this->addRoomTypeIdFilter($query, $room_type_id);
        }

        if ($this->isPaginated($per_page, $page)) {
            return $query->paginate(
                $per_page ?? $this->defaultPerPage,
                ['*'],
                'rooms',
                $page ?? $this->defaultPage
            );
        } else {
            return $query->get();
        }
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
        $nextNumber = DB::table('rooms')
            ->selectRaw('MAX(room_number) + 1 AS lowest_available')
            ->where('departure_id', $departure_id)
            ->where('deleted_at', null)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('rooms as t2')
                    ->whereRaw('rooms.room_number + 1 = t2.room_number');
            })
            ->pluck('lowest_available')
            ->first();

        // Si $nextNumber es null, asignar el número más alto existente + 1
        if (is_null($nextNumber)) {
            $nextNumber = DB::table('rooms')
                    ->where('departure_id', $departure_id)
                    ->where('deleted_at', null)
                    ->max('room_number') + 1;
        }

        return $nextNumber;
    }

    public function addDepartureIdFilter(&$query, $id)
    {
        $query->orWhere('room.departure_id', $id);
    }

    public function addRoomTypeIdFilter(&$query, $id)
    {
        $query->orWhere('room.room_type_id', $id);
    }
}
