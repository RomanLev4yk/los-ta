<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $property_id
 * @property Carbon $date
 * @property int $quantity
 * @property int $arrival_allowed
 * @property int $departure_allowed
 * @property int $minimum_stay
 * @property int $maximum_stay
 * @property int $version
 *
 * @method static|Builder whereProperty(int $property_id)
 *
 * @mixin Builder
 */
class AvailabilityModel extends Model
{
    protected $table = 'availabilities';

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'date',
        'quantity',
        'arrival_allowed',
        'departure_allowed',
        'minimum_stay',
        'maximum_stay',
        'version',
    ];

    protected $hidden = [
        'property_id',
    ];

    public function scopeWhereProperty(Builder $query, int $property_id): Builder
    {
        return $query->where('property_id', '=', $property_id);
    }
}
