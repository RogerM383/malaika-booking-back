<?php

namespace App\Services;

use App\Exceptions\ClientNotFoundException;
use App\Models\Client;

class ClientService
{
    private Client $model;

    /**
     * @param Client $model
     */
    public function __construct(Client $model)
    {
        $this->model = $model;
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
     * @param $id
     * @return mixed
     * @throws ClientNotFoundException
     */
    public function getById($id): mixed
    {
        return $this->model->find($id) ?? throw new ClientNotFoundException($id);
    }

    public function find($dni, $email)
    {

    }

    public function all(): mixed
    {
        $query = $this->model::query();
        /*if (array_key_exists('categories' , $params)) {
            foreach ($params['categories'] as $cat) {
                $this->addCategoryFilter($query, $cat);
            }
        }*/
        return $query->get();
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
