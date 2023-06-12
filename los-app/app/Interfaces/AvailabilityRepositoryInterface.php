<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface AvailabilityRepositoryInterface
{
    public function getPropertyAvailabilitiesByPeriod(string $propertyId, string $fromDate, string $toDate): Collection;
}
