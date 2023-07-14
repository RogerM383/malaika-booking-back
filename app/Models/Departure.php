<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Departure extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start',
        'final',
        'price',
        'pax_capacity',
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
                'seat',
                'observations')
            ->orderBy('rel_client_departure.updated_at', 'asc')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function activeClients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class,'rel_client_departure')
            //->using(ClientDepartures::class)
            ->withPivot(
                'state',
                'seat',
                'observations')
            //->orderBy('rel_client_departure.updated_at', 'asc')
            ->wherePivot('state', '<=', 4)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function waitingClients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class,'rel_client_departure')
            //->using(ClientDepartures::class)
            ->withPivot(
                'state',
                'seat',
                'observations')
            ->wherePivot('state', '=', 6)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function canceledClients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class,'rel_client_departure')
            //->using(ClientDepartures::class)
            ->withPivot(
                'state',
                'seat',
                'observations')
            ->wherePivot('state', '=', 5)
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * @return Collection
     */
    public function assignedRoomsCount(): Collection
    {
        return $this->rooms()
            ->groupBy('room_type_id')
            ->select('rooms.room_type_id', DB::raw('count(*) as quantity'))
            ->get();
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
     * Check if departure available slots is equal or more than required slots
     *
     * @param $required
     * @return bool
     */
    public function hasEnoughSpace($required): bool
    {
        return $this->availableSlots() >= $required;
    }

    /**
     * @return mixed
     */
    public function availableSlots(): mixed
    {
        return $this->pax_capacity - $this->clients()->count();
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
