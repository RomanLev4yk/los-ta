<?php

namespace App\Repositories;

use App\Models\PriceModel;
use App\Interfaces\PriceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PriceRepository implements PriceRepositoryInterface
{
    public function getPricesData(string $propertyId, string $date, int $dayOfWeek, int $personsNumber): Collection
    {
        return PriceModel::property($propertyId)
            ->where(function ($query) use ($date) {
                $query->where('period_from', '<=', $date)
                    ->where('period_till', '>=', $date);
            })
            ->dayOfWeekLike($dayOfWeek)
            ->personsLike($personsNumber)
            ->get();
    }

    public function issetDatePrice(string $propertyId, string $date, int $dayOfWeek, int $personsNumber): bool
    {
        return PriceModel::property($propertyId)
            ->where(function ($query) use ($date) {
                $query->where('period_from', '<=', $date)
                    ->where('period_till', '>=', $date);
            })
            ->dayOfWeekLike($dayOfWeek)
            ->personsLike($personsNumber)
            ->exists();
    }
}
