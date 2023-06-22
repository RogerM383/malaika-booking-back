<?php

namespace App\Services;

use App\Models\ClientType;
use Illuminate\Database\Eloquent\Collection;
use JetBrains\PhpStorm\Pure;

class ClientTypeService extends ResourceService
{
    /**
     * @param ClientType $model
     */
    #[Pure] public function __construct(ClientType $model)
    {
        parent::__construct($model);
    }
}
