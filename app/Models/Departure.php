<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Departure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start',
        'final',
        'price',
        'pax_available',
        'individual_supplement',
        'state',
        'commentary',
        'number_room',
        'type_room',
        'expedient'
    ];

    public function setFinalAttribute($value)
    {
        $this->attributes['final'] = date('Y-m-d',strtotime($value));
    }

    public function setStartAttribute($value)
    {
        $this->attributes['start'] = date('Y-m-d',strtotime($value));
    }

    /**
     * @return HasOne
     */
    public function state(): HasOne
    {
        return $this->hasOne(DepartureState::class);
    }

    /**
     * @return BelongsTo
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::Class);
    }

    /**
     * @return BelongsToMany
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class,'rel_departure_client')
            ->withPivot(
                'state',
                'number_room',
                'type_room',
                'observations')
            ->withTimestamps()
            ->orderBy('rel_departure_client.updated_at', 'asc');
    }

    /**
     * @return BelongsToMany
     */
    public function clientSortRoom(): BelongsToMany
    {
        return $this->belongsToMany(Client::class,'rel_departure_client')
            ->withPivot(
                'state',
                'number_room',
                'type_room',
                'observations')
            ->withTimestamps()
            ->orderBy('rel_departure_client.number_room', 'asc');
    }

    /**
     * @return BelongsToMany
     */
    public function clientsExports(): BelongsToMany
    {
        return $this->belongsToMany(Client::class,'rel_departure_client')
            //->wherePivot('state','<',4)
            ->withPivot(
                'state',
                'number_room',
                'type_room',
                'observations')
            ->withTimestamps()
            ->orderBy('rel_departure_client.number_room', 'asc');
    }
}
