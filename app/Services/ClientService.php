<?php

namespace App\Services;

use App\Exceptions\ClientNotFoundException;
use App\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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

    /**
     * @param $client_type
     * @return array|Collection|LengthAwarePaginator
     */
    public function all(
        $client_type = null,
        $name = null,
        $surname = null,
        $phone = null,
        $email = null,
        $dni = null,
        $passport = null,
        $per_page = null,
        $page = null,
    ): array|Collection|LengthAwarePaginator
    {
        $query = $this->model::query();

        if ($client_type) {
            $this->addClientTypeFilter($query, $client_type);
        }

        if (!empty($name)) {
            $this->addNameFilter($query, $name);
        }

        if (!empty($surname)) {
            $this->addSurnameFilter($query, $surname);
        }

        if (!empty($phone)) {
            $this->addPhoneFilter($query, $phone);
        }

        if (!empty($email)) {
            $this->addEmailFilter($query, $email);
        }

        if (!empty($dni)) {
            $this->addDniFilter($query, $dni);
        }

        if (!empty($passport)) {
            $this->addPassportFilter($query, $passport);
        }

        if ($per_page || $page) {
            return $query->paginate($per_page | 10, ['*'], 'clients', $page | 1);
        } else {
            return $query->get();
        }
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

    /**
     * @param $query
     * @param $id
     * @return void
     */
    public function addClientTypeFilter(&$query, $id)
    {
        $query->whereHas('traveler', function ($q) use ($id) {
            $q->where('travelers.client_type_id', $id);
        });
    }

    public function addNameFilter(&$query, $name)
    {
        $query->orWhere('clients.name', 'LIKE', '%'.$name.'%');
    }

    public function addSurnameFilter(&$query, $surname)
    {
        $query->orWhere('clients.surname', 'LIKE', '%'.$surname.'%');
    }

    public function addPhoneFilter(&$query, $phone)
    {
        $query->orWhere('clients.phone', 'LIKE', '%'.$phone.'%');
    }

    public function addEmailFilter(&$query, $email)
    {
        $query->orWhere('clients.email', 'LIKE', '%'.$email.'%');
    }

    public function addDniFilter(&$query, $dni)
    {
        $query->orWhere('clients.dni', 'LIKE', '%'.$dni.'%');
    }

    public function addPassportFilter(&$query, $number)
    {
        $query->whereHas('passport', function ($q) use ($number) {
            $q->where('passports.number_passport', $number);
        });
    }
}
