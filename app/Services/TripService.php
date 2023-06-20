<?php

namespace App\Services;

use App\Exceptions\TripNotFoundException;
use App\Models\Trip;
use App\Traits\HasPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class TripService extends ResourceService implements ResourceServiceInterface
{
    use HasPagination;

    /**
     * @param Trip $model
     */
    #[Pure] public function __construct(Trip $model)
    {
        parent::__construct($model);
    }

    /**
     * @param null $client
     * @param null $trip_state
     * @param null $per_page
     * @param null $page
     * @return array|LengthAwarePaginator|Collection
     */
    public function all(
        $client = null,
        $trip_state = null,
        $per_page = null,
        $page = null
    ): array|LengthAwarePaginator|Collection
    {
        $query = $this->model::query();

        if ($trip_state) {
            $this->addTripStateId($query, $trip_state);
        }

        /*if ($client_id) {
            $this->addClientIdFilter($query, $client_id);
        }*/

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
     * @throws TripNotFoundException
     */
    public function getById($id): mixed
    {
        return $this->model->find($id) ?? throw new TripNotFoundException($id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws TripNotFoundException
     */
    public function update(int $id, array $data): mixed
    {
        $trip = $this->getById($id);
        $trip->update($data);
        return $trip;
    }

    /**
     * @param $query
     * @param $trip_state
     * @return void
     */
    private function addTripStateId(&$query, $trip_state)
    {
        $query->orWhere('trip_state_id', $trip_state);
    }

    /**
     * @param $query
     * @param $client
     * @return void
     */
    private function addClientIdFilter(&$query, $client)
    {
        $query->whereHas('departures', function ($q) use ($client) {
            $q->where('client_id', '=', $client);
        });
    }

    public function create(array $data): mixed
    {
        // TODO: Implement create() method.
        return null;
    }
}
