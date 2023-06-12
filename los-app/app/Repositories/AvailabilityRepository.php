<?php

namespace App\Repositories;

use App\Models\AvailabilityModel;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\AvailabilityRepositoryInterface;

class AvailabilityRepository implements AvailabilityRepositoryInterface
{
    public function getPropertyAvailabilitiesByPeriod(string $propertyId, string $fromDate, string $toDate): Collection
    {
        return AvailabilityModel::property($propertyId)
            ->betweenDates($fromDate, $toDate)
            ->get();
    }
}
