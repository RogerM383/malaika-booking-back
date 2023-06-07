<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    private User $model;

    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function create($data): mixed
    {
        $data['password'] = bcrypt($data['password']);
        return $this->model->create($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id): mixed
    {
        return $this->model->find($id)->first();
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        return $this->model->find($id)->update($data);
    }
}
