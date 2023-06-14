<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'phone',
        'email',
        'dni',
        'address',
        'dni_expiration',
        'place_birth'
    ];

    /**
     * @return HasOne
     */
    public function passport(): HasOne
    {
        return $this->hasOne(Passport::class);
    }

    /**
     * @return HasOne
     */
    public function traveler(): HasOne
    {
        return $this->hasOne(Traveler::class);
    }

    /**
     * @return BelongsToMany
     */
    public function departures(): BelongsToMany
    {
        return $this->belongsToMany(Departure::class,'rel_client_departure')
            ->withPivot(
                'state',
                    'number_room',
                    'type_room',
                    'observations')
            ->orderBy('rel_client_departure.number_room')
            ->withTimestamps();
    }
}
