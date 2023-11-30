<?php

namespace App\Services;

use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Models\Departure;
use App\Models\Room;
use GuzzleHttp\Psr7\LazyOpenStream;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class DepartureService extends ResourceService
{
    private TripService $tripService;
    private RoomTypeService $roomTypeService;
    private RoomService $roomService;
    private ClientService $clientService;

    /**
     * @param Departure $model
     * @param TripService $tripService
     * @param RoomTypeService $romTypeService
     * @param RoomService $roomService
     * @param ClientService $clientService
     */
    #[Pure] public function __construct(
        Departure $model,
        TripService $tripService,
        RoomTypeService $romTypeService,
        RoomService $roomService,
        ClientService $clientService)
    {
        parent::__construct($model);
        $this->tripService = $tripService;
        $this->roomTypeService = $romTypeService;
        $this->roomService = $roomService;
        $this->clientService = $clientService;
    }

    /**
     * @param null $trip_id
     * @param null $state
     * @param null $per_page
     * @param null $page
     * @return Collection|LengthAwarePaginator
     */
    public function get(
        $trip_id = null,
        $state = null,
        $per_page = null,
        $page = null
    ): Collection|LengthAwarePaginator
    {
        $query = $this->model::query();

        if ($trip_id) {
            $this->addTripIdFilter($query, $trip_id);
        }

        if ($state) {
            $this->addStateFilter($query, $state);
        }

        if ($this->isPaginated($per_page, $page)) {
            return $query->orderBy('id', 'desc')->paginate(
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
        $requestRooms = !empty($rooms) ? collect($rooms) : [];//$roomTypes->map(fn($room, $key) => null);
        // Obtenemos el totasl de plazas
        $total = collect($requestRooms)->map(function ($room, $key) use ($roomTypes) {
            return $roomTypes->has($key) ? $roomTypes[$key] * $room : 0;
        })->sum();
        // Check que no nos pasemos de plazas (puede haber menos xo no más)
        if ($total > $departure->pax_capacity) {
            throw new DeparturePaxCapacityExceededException();
        }
        // Genera la relación entre Departure y RoomType
        /*foreach ($requestRooms as $key => $value) {
            $departure->roomTypes()->attach($key, ["quantity" => $value]);
        }*/

        foreach ($requestRooms as $value) {
            $departure->roomTypes()->attach($value, ["quantity" => 0]);
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

            if ($total > $data['pax_capacity']) {
                throw new DeparturePaxCapacityExceededException();
            }

            /*foreach ($data['rooms'] as $key => $value) {
                $departure->roomTypes()->updateExistingPivot($key, ["quantity" => $value]);
            }*/

            // TODO: mejorar todo esto
            $depRoomTypes = $departure->roomTypes()->get();
            $rels  = $depRoomTypes->pluck('id')->toArray();
            $rooms = array_map('intval', $data['rooms']);

            //$included = array_intersect($rooms, $rels);
            $excluded = array_diff($rels, $rooms);

            foreach ($excluded as $ex) {
                $roomType = $depRoomTypes->pluck('pivot')->where('room_type_id', $ex)->first();
                if ($roomType->quantity === 0) {
                    $departure->roomTypes()->detach($ex);
                }
            }

            foreach ($rooms as $id) {
                $roomType = $depRoomTypes->pluck('pivot')->where('room_type_id', $id)->first();
                if (!$roomType) {
                    $departure->roomTypes()->attach($id, ['quantity' => 0]);
                }
            }
        }

        $departure->update($data);

        return $departure;
    }

    /**
     * @param $id
     * @param $client_id
     * @param $data
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function updateDepartureClient($id, $client_id, $data): mixed
    {
        $departure = $this->getById($id);
        // room_type_id no se actualiza en la relacion ahi se guarda lo que pidio al entrar en el viaje
        $client = $departure->clients()->updateExistingPivot($client_id, Arr::except($data, ['id', 'client_id', 'room_id', 'room_type_id']));
        if (isset($data['state']) || isset($data['room_type_id'])) {
            $this->manageRoom($id, ...Arr::except($data, ['id', 'seat']));
        }
        return $client;
    }

    /**
     * Check if departure available slots is equal or more than required slots
     *
     * @param $id
     * @param $required
     * @return bool
     * @throws ModelNotFoundException
     */
    /*public function hasEnoughSpace($id, $required): bool
    {
        return $this->getAvailableSlots($id) >= $required;
    }*/

    /**
     * @param $id
     * @return mixed
     * @throws ModelNotFoundException
     */
    /*public function getAvailableSlots($id): mixed
    {
        $departure = $this->getById($id);
        return $departure->pax_capacity - $departure->clients()->count();
    }*/

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
     * @return void
     * @throws ModelNotFoundException
     */
    public function removeClient($id, $client_id)
    {
        $departure = $this->getById($id);
        $departure->clients()->detach($client_id);
    }

    /**
     * @param $id
     * @param $clients
     * @return Builder
     * @throws ModelNotFoundException
     */
    public function addClients($id, $clients): Builder
    {

        foreach ($clients as $client) {
            $this->addClient($id, $client);
        }

        $departure = $this->getById($id);
        return $departure->with('clients');
    }

    /**
     * @param $id
     * @param $client
     * @return Room|Model|null
     * @throws ModelNotFoundException
     */
    public function addClient($id, $client): Model|Room|null
    {
        $departure = $this->getById($id);
        $departure->clients()->attach($client['client_id'],  Arr::except($client, ['id', 'room_id', 'client_id']));
        return $this->manageRoom($id, ...Arr::except($client, ['id', 'seat']));
    }

    /**
     * @param $departure_id
     * @param $client_id
     * @param $state
     * @param $room_id
     * @param $room_type_id
     * @param $observations
     * @return Model|Room|null
     * @throws ModelNotFoundException
     */
    private function manageRoom ($departure_id, $client_id = null, $state = null, $room_id = null, $room_type_id = null, $observations = null): Model|Room|null
    {
        $room = null;

        if (!isset($room_id) && isset($room_type_id) && $state <= 4) {
            // Si state es uno de los activos y no tenemos room_id crea habitacion
            $room   = $this->addRoom($departure_id, $room_type_id, $observations);
            $client = $this->clientService->getById($client_id);
            $client->rooms()->attach($room->id);

            // --- Habitacion nueva ----------------------------------------------------- 29/11/23
            // TODO hacerlo mejor, quitar apaño
            $departure = $this->getById($departure_id);
            $departure->roomTypes()->newPivotQuery()->where('room_type_id',$room_type_id)->increment('quantity');

        } else if ($room_id && $state <= 4) {

            // Si state es uno de los activos y tenemos room_id añadimos a la habitacion
            $room = $this->roomService->getById($room_id);
            $client = $this->clientService->getById($client_id);
            $client->rooms()->attach($room_id);
        } else if ($state >= 5) {
            // Si state es waiting o cancelado, nos aseguramos de que no esten en una habitacion
            // si lo esta la eliminamos
            $client = $this->clientService->getById($client_id);
            $client->rooms()->detach($room_id);

            $room = $this->roomService->getById($room_id);
            // Si no tengo room ID es que he de petarla
            // TODO:: Pasar rom_type por param para evitar tener que buscar la room con el service
            /*if ($room_id) {
                $room = $this->roomService->getById($room_id);
                $departure = $this->getById($departure_id);
                $departure->roomTypes()->newPivotQuery()->where('room_type_id',$room->room_type_id)->decrement('quantity',1);
            }*/
        }

        $departure  = $this->getById($departure_id);
        $emptyRooms = $departure->rooms()->doesntHave('clients')->get();

        if ($emptyRooms->count() >= 1) {
            // Si hay habitaciojnes vacias las elimina
            foreach ($emptyRooms as $r)  {
                $departure->roomTypes()->newPivotQuery()->where('room_type_id', $r->room_type_id)->decrement('quantity',1);
                $r->delete();
            }

            //$departure->rooms()->doesntHave('clients')->delete();

            foreach ($departure->rooms()->get() as $key => $r) {
                $r->room_number = $key + 1;
                $r->save();
            }
        }

        return $room;
    }

    /**
     * @param $departure_id
     * @param $room_type_id
     * @param $observations
     * @return Room
     */
    public function addRoom($departure_id, $room_type_id, $observations): Room
    {
        //$departure = $this->getById($id);
        $room = $this->roomService->make([
            'departure_id'  => $departure_id,
            'room_type_id'  => $room_type_id,
            'room_number'   => $this->roomService->getNextRoomNumber($departure_id),
            'observations'  => $observations
        ]);
        $room->save();
        return $room;
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

    /**
     * @param $query
     * @param $state
     * @return void
     */
    private function addStateFilter(&$query, $state)
    {
        $query->where('state_id', $state);
    }
}
