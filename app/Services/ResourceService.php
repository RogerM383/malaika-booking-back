<?php

namespace App\Services;

use App\Exceptions\AppModelNotFoundException;
use App\Exceptions\ClientNotFoundException;
use App\Models\Passport;
use App\Traits\HasPagination;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;

abstract class ResourceService
{
    use HasPagination;

    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
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
     * @return mixed
     */
    public function all(): mixed
    {
        return $this->model->get();
    }

    /**
     * @param $id
     * @return mixed
     * @throws AppModelNotFoundException
     */
    public function getById($id): mixed
    {
        return $this->model->find($id) ?? throw new AppModelNotFoundException($id);
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
     * @throws AppModelNotFoundException
     */
    public function update(int $id, array $data): mixed
    {
        $model = $this->getById($id);
        $model->update($data);
        return $model;
    }
}
