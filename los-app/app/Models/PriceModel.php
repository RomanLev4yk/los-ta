<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $property_id
 * @property int $duration
 * @property int $amount
 * @property string $currency
 * @property string $persons
 * @property string $weekdays
 * @property int $minimum_stay
 * @property int $maximum_stay
 * @property int $extra_person_price
 * @property string $extra_person_price_currency
 * @property Carbon $period_from
 * @property Carbon $period_till
 * @property int $version
 *
 * @method static|Builder whereProperty(int $property_id)
 *
 * @mixin Builder
 */
class PriceModel extends Model
{
    protected $table = 'prices';

    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'duration',
        'amount',
        'currency',
        'persons',
        'weekdays',
        'minimum_stay',
        'maximum_stay',
        'extra_person_price',
        'extra_person_price_currency',
        'period_from',
        'period_till',
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
