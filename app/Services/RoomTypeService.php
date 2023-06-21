<?php

namespace App\Services;

use App\Exceptions\AppModelNotFoundException;
use App\Models\RoomType;
use JetBrains\PhpStorm\Pure;

class RoomTypeService extends ResourceService
{
    /**
     * @param RoomType $model
     */
    #[Pure] public function __construct(RoomType $model)
    {
        parent::__construct($model);
    }
}
