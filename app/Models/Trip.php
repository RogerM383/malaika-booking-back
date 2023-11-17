<?php

namespace App\Models;

use App\Traits\Slugeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes, Slugeable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        //'category',
        'commentary',
        'trip_state_id',
        'slug',
        'open_date',
        'before_open_text',
        'after_close_text',
        'closed',
        'image',
        'pdf'
    ];

    /**
     * @return HasMany
     */
    public function departures(): HasMany
    {
        return $this->hasMany(Departure::class);
    }

    /**
     * @return BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(TripState::class, 'trip_state_id');
    }
}
