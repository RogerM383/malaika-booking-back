<?php

namespace App\Services;

use App\Exceptions\ClientNotFoundException;
use App\Models\Client;
use App\Traits\HasPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class ClientService extends ResourceService
{
    use HasPagination;

    /**
     * @param Client $model
     */
    #[Pure] public function __construct(Client $model)
    {
        parent::__construct($model);
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
        return $this->model->firstOrCreate(
            ['email' => $data['email'], 'dni' => $data['dni']],
            $data
        );
        //return $this->model->create($data);
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

    /**
     * @param null $client_type
     * @param null $name
     * @param null $surname
     * @param null $phone
     * @param null $email
     * @param null $dni
     * @param null $passport
     * @param null $per_page
     * @param null $page
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

        if ($this->isPaginated($per_page, $page)) {
            return $query->paginate(
                $per_page ?? $this->defaultPerPage,
                ['*'],
                'clients',
                $page ?? $this->defaultPage
            );
        } else {
            return $query->get();
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws ClientNotFoundException
     */
    public function update(int $id, array $data): mixed
    {
        $client = $this->getById($id);
        $client->update($data);
        return $client;
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
