<?php

namespace App\Services;

use App\Exceptions\DepartureNotFoundException;
use App\Exceptions\TripNotFoundException;
use App\Models\Departure;
use App\Traits\HasPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class DepartureService extends ResourceService
{
    use HasPagination;

    private $tripService;

    /**
     * @param Departure $model
     */
    #[Pure] public function __construct(Departure $model, TripService $tripService)
    {
        parent::__construct($model);
        $this->tripService = $tripService;
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
     * @throws TripNotFoundException
     */
    public function create($data): mixed
    {
        $trip = $this->tripService->getById($data['trip_id']);
        return $trip->departures()->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws DepartureNotFoundException
     */
    public function update(int $id, array $data): mixed
    {
        $client = $this->getById($id);
        $client->update($data);
        return $client;
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
