<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface PriceRepositoryInterface
{
    public function getPricesData(string $propertyId, string $date, int $dayOfWeek, int $personsNumber): Collection;
}
