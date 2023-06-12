<?php

namespace App\Repositories;

use App\Models\PriceModel;
use App\Interfaces\PriceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PriceRepository implements PriceRepositoryInterface
{
    public function getPricesData(string $propertyId, string $date, int $dayOfWeek, int $personsNumber): Collection
    {
        return PriceModel::where('property_id', $propertyId)
            ->where(function ($query) use ($date) {
                $query->where('period_from', '<=', $date)
                    ->where('period_till', '>=', $date);
            })
            ->where('weekdays', 'LIKE', '%'. $dayOfWeek .'%')
            ->where('persons', 'LIKE', '%'. $personsNumber .'%')
            ->get();
    }
}
