<?php

namespace App\Services;

use App\Exceptions\ModelNotFoundException;
use App\Exceptions\PassportNotFoundException;
use App\Models\Passport;
use App\Traits\HasPagination;
use JetBrains\PhpStorm\Pure;

class PassportService
{
    use HasPagination;

    private Passport $model;

    /**
     * @param Passport $model
     */
    public function __construct(Passport $model)
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
     * @throws PassportNotFoundException
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): mixed
    {
        $model = $this->model->find($id) ?? throw new PassportNotFoundException($id);
        $model->update($data);
        return $model;
    }
}
