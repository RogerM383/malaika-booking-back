<?php

namespace App\Services;

use App\Exceptions\ModelNotFoundException;
use App\Models\Passport;
use App\Traits\HasPagination;
use JetBrains\PhpStorm\Pure;

class PassportService extends ResourceService
{
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
     * @param array $data
     * @return mixed
     * @throws ModelNotFoundException
     */
    public function create(array $data): mixed
    {
        $clientId = $data['client_id'];
        $client = $this->clientService->getById($clientId);
        return $client->passport()->firstOrCreate(
            ['client_id' => $clientId],
            $data,
        );
    }
}
