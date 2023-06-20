<?php

namespace App\Services;

use App\Exceptions\AppModelNotFoundException;
use App\Exceptions\DepartureNotFoundException;
use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\MaxDeparturePaxCapacityExceededException;
use App\Exceptions\TripNotFoundException;
use App\Models\Departure;
use App\Models\RoomType;
use App\Traits\HasPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class DepartureService extends ResourceService
{
    use HasPagination;

    private TripService $tripService;
    private RoomTypeService $roomTypeService;

    /**
     * @param Departure $model
     */
    #[Pure] public function __construct(Departure $model, TripService $tripService, RoomTypeService $romTypeService)
    {
        parent::__construct($model);
        $this->tripService = $tripService;
        $this->roomTypeService = $romTypeService;
    }

    /**
     * @param null $trip_id
     * @param null $per_page
     * @param null $page
     * @return array|LengthAwarePaginator|Collection
     */
    public function all(
        $trip_id = null,
        $per_page = null,
        $page = null
    ): array|LengthAwarePaginator|Collection
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
     * @param $id
     * @return mixed
     * @throws DepartureNotFoundException
     */
    public function getById($id): mixed
    {
        return $this->model->find($id) ?? throw new DepartureNotFoundException($id);
    }

    /**
     * @param $data
     * @return mixed
     * @throws DeparturePaxCapacityExceededException|TripNotFoundException
     */
    public function create($data): mixed
    {
        $rooms = isset($data['rooms']) ? $data['rooms'] : null;
        // Obtenemos el Trip
        $trip = $this->tripService->getById($data['trip_id']);
        // Le creamos una nueva Departure
        $departure = $trip->departures()->create($data);
        // Obtenemos todos los RoomType con su ID y capaciodad
        $roomTypes = $this->roomTypeService->all()->pluck('capacity', 'id');
        // Obtenemos los tipos pasados en la request o genera una array con todos los tipos de la DB con valor null
        $requestRooms = !empty($rooms) ? collect($rooms) : $roomTypes->map(fn($room, $key) => null);
        // Obtenemos el totasl de plazas
        $total = collect($requestRooms)->map(function ($room, $key) use ($roomTypes) {
            return $roomTypes->has($key) ? $roomTypes[$key] * $room : 0;
        })->sum();
        // Check que no nos pasemos de plazas (puede haber menos xo no más)
        if ($total > $departure->pax_available) {
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
     * @return mixed
     * @throws DepartureNotFoundException
     * @throws AppModelNotFoundException
     * @throws DeparturePaxCapacityExceededException
     */
    public function update(int $id, array $data): mixed
    {
        $departure = $this->getById($id);

        // TODO: falta checkear si hay habitacione ya asignadas a la salida con clientres dentro
        // Que pasa si las eliminamos? perdemos  ala agente asignada? Quedan sin asignar?

        if (isset($data['rooms'])) {
            $roomTypes = $this->roomTypeService->all()->pluck('capacity', 'id');

            $total = collect($data['rooms'])->map(function ($room, $key) use ($roomTypes) {
                return $roomTypes->has($key) ? $roomTypes[$key] * $room : 0;
            })->sum();

            if ($total > $departure->pax_available) {
                throw new DeparturePaxCapacityExceededException();
            }

            foreach ($data['rooms'] as $key => $value) {
                $departure->roomTypes()->updateExistingPivot($key, ["quantity" => $value]);
            }
        }

        $departure->update($data);

        return $departure;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DepartureNotFoundException
     */
    public function getDepartureRoomingData(int $id)
    {
        $departure = $this->getById($id);
        return $departure->with('clients')->get();
    }

    public function addClient($id, $client_id)
    {
        $departure = $this->getById($id);

        // 1.- Mirar si ya existe el usuario

        $departure->clients()->attach($client_id);
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
