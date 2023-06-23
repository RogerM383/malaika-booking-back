<?php

namespace App\Services;

use App\Exceptions\AppModelNotFoundException;
use App\Exceptions\DepartureNotFoundException;
use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Models\Departure;
use App\Models\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;

class DepartureService extends ResourceService
{
    private TripService $tripService;
    private RoomTypeService $roomTypeService;
    private RoomService $roomService;

    /**
     * @param Departure $model
     * @param TripService $tripService
     * @param RoomTypeService $romTypeService
     * @param RoomService $roomService
     */
    #[Pure] public function __construct(
        Departure $model,
        TripService $tripService,
        RoomTypeService $romTypeService,
        RoomService $roomService)
    {
        parent::__construct($model);
        $this->tripService = $tripService;
        $this->roomTypeService = $romTypeService;
        $this->roomService = $roomService;
    }

    /**
     * @param null $trip_id
     * @param null $per_page
     * @param null $page
     * @return Collection|LengthAwarePaginator
     */
    public function get(
        $trip_id = null,
        $per_page = null,
        $page = null
    ): Collection|LengthAwarePaginator
    {
        $query = $this->model::query();

        if ($trip_id) {
            $this->addTripIdFilter($query, $trip_id);
        }

        if ($this->isPaginated($per_page, $page)) {
            return $query->paginate(
                $per_page ?? $this->defaultPerPage,
                ['*'],
                'trips',
                $page ?? $this->defaultPage
            );
        } else {
            return $query->get();
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function make($data): mixed
    {
        return $this->model::firstOrNew(
            ['dni' => $data['dni']],
            $data
        );
    }

    /**
     * @param $data
     * @return mixed
     * @throws ModelNotFoundException|DeparturePaxCapacityExceededException
     */
    public function create($data): mixed
    {
        $rooms = $data['rooms'] ?? null;
        // Obtenemos el Trip
        $trip = $this->tripService->getById($data['trip_id']);
        // Le creamos una nueva Departure
        $departure = $trip->departures()->create($data);
        // Obtenemos todos los RoomType con su ID y capaciodad
        $roomTypes = $this->roomTypeService->get()->pluck('capacity', 'id');
        // Obtenemos los tipos pasados en la request o genera una array con todos los tipos de la DB con valor null
        $requestRooms = !empty($rooms) ? collect($rooms) : $roomTypes->map(fn($room, $key) => null);
        // Obtenemos el totasl de plazas
        $total = collect($requestRooms)->map(function ($room, $key) use ($roomTypes) {
            return $roomTypes->has($key) ? $roomTypes[$key] * $room : 0;
        })->sum();
        // Check que no nos pasemos de plazas (puede haber menos xo no más)
        if ($total > $departure->pax_capacity) {
            throw new DeparturePaxCapacityExceededException();
        }
        // Genera la relación entre Departure y RoomType
        foreach ($requestRooms as $key => $value) {
            $departure->roomTypes()->attach($key, ["quantity" => $value]);
        }
        // Retorna resultado
        return $departure;
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     * @throws DeparturePaxCapacityExceededException|ModelNotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $departure = $this->getById($id);

        // TODO: falta checkear si hay habitacione ya asignadas a la salida con clientres dentro
        // Que pasa si las eliminamos? perdemos  ala agente asignada? Quedan sin asignar?

        if (isset($data['rooms'])) {
            $roomTypes = $this->roomTypeService->get()->pluck('capacity', 'id');

            $total = collect($data['rooms'])->map(function ($room, $key) use ($roomTypes) {
                return $roomTypes->has($key) ? $roomTypes[$key] * $room : 0;
            })->sum();

            if ($total > $departure->pax_capacity) {
                throw new DeparturePaxCapacityExceededException();
            }

            foreach ($data['rooms'] as $key => $value) {
                $departure->roomTypes()->updateExistingPivot($key, ["quantity" => $value]);
            }
        }

        $departure->update($data);

        return $departure;
    }

    public function getAvailableSlots($id)
    {
        $departure = $this->getById($id);
        return $departure->pax_capacity - $departure->clients()->count();
    }

    // -----------------------

    /**
     * @param int $id
     * @return mixed
     */
    /*public function getDepartureRoomingData(int $id)
    {
        $departure = $this->getById($id);
        return $departure->with('clients')->get();
    }*/

    /**
     * @param $id
     * @param $client_id
     * @param $room_type_id
     * @return Builder
     * @throws ModelNotFoundException
     */
    public function addClient($id, $client_id, $room_type_id): Builder
    {
        $departure = $this->getById($id);
        $departure->clients()->attach($client_id, ['room_type_id' => $room_type_id]);
        return $departure->with('clients');
    }

    /**
     * @param $id
     * @param $client_id
     * @param $room_type_id
     * @param $observations
     * @return Room
     * @throws ModelNotFoundException
     */
    public function addRoom($id, $client_id, $room_type_id, $observations): Room
    {
        $departure = $this->getById($id);
        $room = $this->roomService->make([
            'room_type_id'  => $room_type_id,
            'room_number'   => $this->roomService->getNextRoomNumber($departure->id),
            'observations'  => $observations
        ]);
        //$room->cliets()->
        //return $this->roomService->createInDeparture($departure, $client_id, $room_type_id, $observations);
    }

    /**
     * @param $query
     * @param $trip_id
     * @return void
     */
    private function addTripIdFilter(&$query, $trip_id)
    {
        $query->whereHas('trip', function ($q) use ($trip_id) {
            return $q->where('trip_id', $trip_id);
        });
    }
}
