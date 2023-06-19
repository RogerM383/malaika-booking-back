<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientDepartures extends Pivot
{
    protected $table = 'rel_client_departure';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}
