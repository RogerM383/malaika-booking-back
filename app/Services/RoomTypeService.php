<?php

namespace App\Services;

use App\Exceptions\AppModelNotFoundException;
use App\Models\RoomType;
use JetBrains\PhpStorm\Pure;

class RoomTypeService
{
    protected RoomType $model;

    /**
     * @param RoomType $model
     */
    #[Pure] public function __construct(RoomType $model)
    {
        $this->model = $model;
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
        return $this->model->find($id) ?? throw new AppModelNotFoundException($id);
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
}
