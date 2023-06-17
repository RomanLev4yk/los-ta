<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\PriceModel;
use App\Repositories\PriceRepository;

class RetrievePricesTest extends TestCase
{
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

    public function test_price_by_date_exists(): void
    {
        $propertyId = '71438849-47cb-4b00-82de-34fff691f017';
        $date = '2017-05-18';
        $persons = 1;

        $repository = new PriceRepository();
        $priceExist = $repository->issetDatePrice(
            $propertyId,
            $date,
            Carbon::parse($date)->dayOfWeek,
            $persons
        );

        $this->assertTrue($priceExist);
    }
}
