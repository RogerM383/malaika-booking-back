<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes;

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
    public function trips(): HasMany
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

    /**
     * @return BelongsToMany
     */
    public function formRoomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'form_departure_room_type', 'room_type_id', 'departure_id')
            ->withPivot(
                'quantity'
            )
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
