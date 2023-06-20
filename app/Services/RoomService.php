<?php

namespace App\Services;

use App\Exceptions\AppModelNotFoundException;
use App\Exceptions\DepartureNotFoundException;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class RoomService extends ResourceService implements ResourceServiceInterface
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
     * @return mixed
     */
    public function all(): mixed
    {
        $query = $this->model::query();
        return $query->get();
    }

    /**
     * @param $id
     * @return mixed
     * @throws AppModelNotFoundException
     */
    public function getById($id): mixed
    {
        return $this->model->find($id) ?? throw new AppModelNotFoundException($id, 'Room with id '.$id.' doesen\'t exists');
    }

    /**
     * @param $ids
     * @return mixed
     * @throws AppModelNotFoundException
     */
    public function getByIds($ids): mixed
    {
        return $this->model::find($ids) ?? throw new AppModelNotFoundException($ids, 'RoomType with ids '.join(',', $ids).' doesen\'t exists');
    }

    /**
     * @param $departure_id
     * @param $client_id
     * @param $room_type_id
     * @param $observations
     * @return Room
     * @throws DepartureNotFoundException
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

    /**
     * @param $data
     * @return mixed
     */
    public function create($data): mixed
    {
        return null;
    }

    public function update(int $id, array $data): mixed
    {
        // TODO: Implement update() method.
        return null;
    }
}
