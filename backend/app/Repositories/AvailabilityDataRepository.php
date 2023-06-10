<?php

namespace App\Repositories;

use App\Models\PriceModel;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\AvailabilityRepositoryInterface;

class AvailabilityDataRepository implements AvailabilityRepositoryInterface
{
    public function getStayPricingData(int $fromDate, int $toDate): Collection
    {
        return PriceModel::all();
    }
}