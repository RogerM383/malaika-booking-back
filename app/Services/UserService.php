<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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
        $isAdmin = Arr::pull($data, 'is_admin');
        $data['password'] = bcrypt($data['password']);
        $user = $this->model->create($data);

        if (!empty($isAdmin)) {
            $user->roles()->attach(1);
        }
        return $user;
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
        $isAdmin = Arr::pull($data, 'is_admin');

        if ($data['password'] !== null)
            $data['password'] = bcrypt($data['password']);

        $user = $this->model->find($id)->update($data);

        if (!empty($isAdmin)) {
            $user->roles()->attach(1);
        }

        return $user;
    }
}
