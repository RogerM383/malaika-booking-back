<?php

namespace App\Services;

use App\Exceptions\TravelerNotFoundException;
use App\Models\Traveler;
use App\Traits\HasPagination;
use JetBrains\PhpStorm\Pure;

class TravelerService
{
    use HasPagination;

    private Traveler $model;

    /**
     * @param Traveler $model
     */
    public function __construct(Traveler $model)
    {
        $this->model = $model;
    }

    /**
     * @return string[]
     */
    #[Pure] public function getFillable(): array
    {
        return $this->model->getFillable();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function create($data): mixed
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws TravelerNotFoundException
     */
    public function update(int $id, array $data): mixed
    {
        $model = $this->model->find($id) ?? throw new TravelerNotFoundException($id);
        $model->update($data);
        return $model;
    }
}
