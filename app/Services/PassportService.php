<?php

namespace App\Services;

use App\Exceptions\ClientNotFoundException;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\PassportNotFoundException;
use App\Models\Passport;
use App\Traits\HasPagination;
use JetBrains\PhpStorm\Pure;

class PassportService extends ResourceService
{
    use HasPagination;

    private Passport $model;
    private ClientService $clientService;

    /**
     * @param Passport $model
     * @param ClientService $clientService
     */
    #[Pure] public function __construct(Passport $model, ClientService $clientService)
    {
        parent::__construct($model);
        $this->clientService = $clientService;
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
     * @throws ClientNotFoundException
     */
    public function create($data): mixed
    {
        $client = $this->clientService->getById($data['client_id']);
        return $client->passport()->create($data);
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
