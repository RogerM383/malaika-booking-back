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
     * @param $data
     * @return mixed
     * @throws ClientNotFoundException
     */
    public function create($data): mixed
    {
        $clientId = $data['client_id'];
        $client = $this->clientService->getById($clientId);
        return $client->passport()->firstOrCreate(
            ['client_id' => $clientId],
            $data,
        );
        //return $client->passport()->create($data);
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
