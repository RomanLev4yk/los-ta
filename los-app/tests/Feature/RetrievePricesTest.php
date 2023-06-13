<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PriceModel;
use App\Repositories\PriceRepository;

class RetrievePricesTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_retrieve_prices_by_date(): void
    {
        $propertyId = '71438849-47cb-4b00-82de-34fff691f017';
        $date = '2017-07-06';
        $dayOfWeek = 6;
        $persons = 1;

        $repository = new PriceRepository();
        $prices = $repository->getPricesData(
            $propertyId,
            $date,
            $dayOfWeek,
            $persons
        );

        $prices->each(function ($price) {
            $this->assertTrue($price instanceof PriceModel);
        });
    }
}
