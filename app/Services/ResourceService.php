<?php

namespace App\Services;

use App\Exceptions\ModelNotFoundException;
use App\Traits\HasPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class ResourceService
{
    use HasPagination;

    protected $model;

    /**
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getFillable(): array
    {
        return $this->model->getFillable();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function make(array $data): mixed
    {
        return $this->model::make($data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        return $this->model::create($data);
    }

    /**
     * @return Collection|LengthAwarePaginator
     */
    public function get(): Collection|LengthAwarePaginator
    {
        return $this->model::all();
    }

    /**
     * @param array|int $ids
     * @return Model
     * @throws ModelNotFoundException
     */
    public function getById(array|int $ids): Model
    {
        return $this->model::find($ids) ?? throw new ModelNotFoundException($this->model, $ids);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): mixed
    {
        $model = $this->getById($id);
        $model->update($data);
        return $model;
    }

    /**
     * @param $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete ($id): bool
    {
        return $this->getById($id)->delete();
    }
}
