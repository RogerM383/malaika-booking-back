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
        'state_id',
        'commentary',
        'expedient',
        'taxes',
    ];

    /*public function setFinalAttribute($value)
    {
        $this->attributes['final'] = date('Y-m-d',strtotime($value));
    }

    public function setStartAttribute($value)
    {
        $this->attributes['start'] = date('Y-m-d',strtotime($value));
    }*/

    /**
     * @return BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(DepartureState::class);
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
        return $this->belongsToMany(Client::class,'rel_client_departure')
            ->using(ClientDepartures::class)
            ->withPivot(
                'state',
                //'number_room',
                //'room_type_id',
                'observations')
            ->withTimestamps()
            ->orderBy('rel_client_departure.updated_at', 'asc');
    }

    /**
     * @return BelongsToMany
     */
    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'rel_departure_room_type')
            ->withPivot(
                'quantity'
            )
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    /*public function clientSortRoom(): BelongsToMany
    {
        return $this->belongsToMany(Client::class,'rel_client_departure')
            ->withPivot(
                'state',
                'number_room',
                'type_room',
                'observations')
            ->withTimestamps()
            ->orderBy('rel_client_departure.number_room', 'asc');
    }*/

    /**
     * @return BelongsToMany
     */
    /*public function clientsExports(): BelongsToMany
    {
        return $this->belongsToMany(Client::class,'rel_client_departure')
            //->wherePivot('state','<',4)
            ->withPivot(
                'state',
                'number_room',
                'type_room',
                'observations')
            ->withTimestamps()
            ->orderBy('rel_client_departure.number_room', 'asc');
    }*/
}
