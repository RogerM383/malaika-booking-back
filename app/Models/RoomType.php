<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return HasMany
     */
    public function tips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * @return BelongsToMany
     */
    public function departures(): BelongsToMany
    {
        return $this->belongsToMany(Departure::class, 'rel_departure_room_type')->withTimestamps();
    }
}
