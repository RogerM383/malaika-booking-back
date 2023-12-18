<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

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
        'place_birth',
        'observations',
        'seat',
        'deleted_at',
        'room_observations'
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
    public function clientType(): BelongsTo
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
            ->using(ClientDepartures::class)
            ->withPivot(
                'state',
                /*'number_room',*/
                'room_type_id',
                //'seat',
                'observations')
                //'room_observations')
            //->orderBy('rel_client_departure.number_room')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function rooms (): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'rel_client_room')->withTimestamps();
    }

    public function scopeRoom($query, $departureId)
    {
        return $query->with(['rooms' => function ($roomQuery) use ($departureId) {
            $roomQuery->where('departure_id', $departureId);
        }]);
    }

    ///**
    // *
    // */
    //public function scopeRoom ($builder, int $departureId)
    //{
    //    if (is_null($builder->getQuery()->columns)) {
    //        $builder->select('clients.*');
    //    }
//
    //    $builder
    //        /*->addSelect([
    //            'rooms.room_type_id',
    //            'rooms.room_number',
    //            'rooms.observations',
    //        ])*/
    //        ->with(['rooms' => function ($query) use ($departureId) {
    //            $query->where('departure_id', $departureId);
    //        }]);
//
    //    /*$builder->addSelect([
    //            'rooms.room_type_id',
    //            'rooms.room_number',
    //            'rooms.observations',
    //        ])
    //        ->join('rel_client_room as rlp', function ($join) use ($lang) {
    //            $join->on('rlp.product_id', '=', 'products.id')
    //                ->where('rlp.language_id', $lang);
    //        });*/
    //}
}
/*addGlobalScope('translate', function (Builder $builder) {

    $lang = Auth::user()->language_id ?? Session::get('lang') ?? config('app.dafault_lang_id');

    if (is_null($builder->getQuery()->columns)) {
        $builder->select('products.*');
    }

    $builder->addSelect([
        'rlp.name',
        'rlp.slug',
        'rlp.description',
        'rlp.long_description',
    ])
        ->join('rel_languages_products as rlp', function ($join) use ($lang) {
            $join->on('rlp.product_id', '=', 'products.id')
                ->where('rlp.language_id', $lang);
        });
});*/
