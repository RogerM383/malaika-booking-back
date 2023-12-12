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
            return $query->orderBy('departures.start', 'desc')->paginate(
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
        $formRooms = $data['form_rooms'] ?? [];
        // Obtenemos todos los RoomType con su ID y capaciodad
        $roomTypes = $this->roomTypeService->get()->pluck('capacity', 'id');

        // Calcula el maximo de slots
        /*$total = collect($formRooms)->map(function ($room) use ($roomTypes) {
            return $roomTypes->has($room['id']) ? $roomTypes[$room['id']] * $room['quantity'] : 0;
        })->sum();*/

        // Check que no nos pasemos de plazas (puede haber menos xo no más)
        /*if ($total > $data['pax_capacity']) {
            throw new DeparturePaxCapacityExceededException();
        }*/

        // Obtenemos el Trip
        $trip = $this->tripService->getById($data['trip_id']);
        // Le creamos una nueva Departure
        $departure = $trip->departures()->create($data);


        // --- ROOMS (controla numero asociado a la departure) ---------------------------------------------------------
        // Genera la relación entre Departure y RoomType
        foreach ($roomTypes as $key => $value) {
            $departure->roomTypes()->attach($key, ["quantity" => 0]);
        }

        // --- FORM ROOMS (controla numero y maximos asociados al formulario inscripcion) ------------------------------
        $formRooms = !empty($formRooms) ? collect($formRooms) : [];
        foreach ($formRooms as $value) {
            $departure->formRoomTypes()->attach($value['id'], ['quantity' => $value['quantity']]);
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

        // --- CONTROL ROOMS DE FORMULARIO -----------------------------------------------------------------------------
        if (isset($data['form_rooms'])) {

            $formRooms = $data['form_rooms'];
            $roomTypes = $this->roomTypeService->get()->pluck('capacity', 'id');

            // Calcula el maximo de slots
            /*$total = collect($formRooms)->map(function ($room) use ($roomTypes) {
                return $roomTypes->has($room['id']) ? $roomTypes[$room['id']] * $room['quantity'] : 0;
            })->sum();
            $capacity = $data['pax_capacity'] ?? $departure->pax_capacity;
            if ($total > $capacity) {
                throw new DeparturePaxCapacityExceededException();
            }*/

            // TODO: mejorar todo esto
            // --- Pilla tipos de habitacion permitidas en el form -----------------------------------------------------
            $depRoomTypes = $departure->formRoomTypes()->get();
            // --- Obtiene IDs de las relaciones (tabla intermedia) ----------------------------------------------------
            $rels  = $depRoomTypes->pluck('id')->toArray();

            $rooms = collect($formRooms)->mapWithKeys(function ($room) {
                return [
                    $room['id'] => [ 'quantity' => intval($room['quantity']) ]
                ];
            });

            $departure->formRoomTypes()->sync($rooms);
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
            $room = $this->addRoom($departure_id, $room_type_id, $observations);
            $client = $this->clientService->getById($client_id);
            $client->rooms()->attach($room->id);

            // --- Habitacion nueva ----------------------------------------------------- 29/11/23
            // TODO hacerlo mejor, quitar apaño
            $departure = $this->getById($departure_id);
            $departure->roomTypes()->newPivotQuery()->where('room_type_id', $room_type_id)->increment('quantity');

        } else if (isset($room_id) && isset($room_type_id) && $state <= 4) {

            // Si state es uno de los activos && estramos modificando el tipo de una habitacion
            $room = $this->roomService->getById($room_id);
            $oldRoomTypeId = $room->room_type_id;

            if ($oldRoomTypeId !== $room_type_id) {
                // --- Cambio de tipo de habitacion ------------------------------------------ 03/12/23
                // TODO hacerlo mejor, quitar apaño
                $departure = $this->getById($departure_id);

                // TODO: check si la capacidad sigue siendo menor que pax
                $departure->roomTypes()
                    ->newPivotQuery()
                    ->where('room_type_id', $oldRoomTypeId)
                    ->where('quantity', '>=', 1)
                    ->decrement('quantity', 1);
                $departure->roomTypes()->newPivotQuery()->where('room_type_id', $room_type_id)->increment('quantity');

                // save room
                $room->room_type_id = $room_type_id;
                $room->save();
            }
        } else if ($room_id && $state <= 4) {

            // Si state es uno de los activos y tenemos room_id añadimos a la habitacion
            $room = $this->roomService->getById($room_id);
            $client = $this->clientService->getById($client_id);
            $client->rooms()->attach($room_id);
        } else if ($state >= 5) {
            // Si state es waiting o cancelado, nos aseguramos de que no esten en una habitacion
            // si lo esta la eliminamos
            $client = $this->clientService->getById($client_id);

            Log::debug('DETACHING '.$room_id);

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
                $departure->roomTypes()
                    ->newPivotQuery()
                    ->where('room_type_id', $r->room_type_id)
                    ->where('quantity', '>=', 1)
                    ->decrement('quantity',1);
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
