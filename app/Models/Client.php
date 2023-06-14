<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'notes',
        'intolerances',
        'frequent_flyer',
        'member_number',
        'client_type_id',
        'language_id',
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
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * @return BelongsTo
     */
    public function clientTypes(): BelongsTo
    {
        return $this->belongsTo(ClientType::class, 'client_type_id');
    }

    /**
     * @return BelongsToMany
     */
    public function departures(): BelongsToMany
    {
        // TODO: mirar como meter las relaciones intermedias, room_type_id, state_id
        return $this->belongsToMany(Departure::class,'rel_client_departure')
            ->withPivot(
                'state',
                'number_room',
                'type_room',
                'seat',
                'observations',
                'room_observations')
            ->orderBy('rel_client_departure.number_room')
            ->withTimestamps();
    }
}
