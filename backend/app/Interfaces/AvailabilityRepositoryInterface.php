<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface AvailabilityRepositoryInterface
{
    public function getStayPricingData(int $fromDate, int $toDate): Collection;
}