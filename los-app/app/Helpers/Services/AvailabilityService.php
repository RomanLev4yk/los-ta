<?php

namespace App\Helpers\Services;

use App\Models\PriceModel;
use App\Models\AvailabilityModel;
use App\Repositories\PriceRepository;
use App\Repositories\AvailabilityRepository;
use App\Interfaces\PriceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class AvailabilityService
{
    private int $numberOfPersons = 3;
    private int $numberOfDays = 21;

    private PriceRepositoryInterface $priceRepository;
    private AvailabilityRepository $availabilityRepository;

    public function __construct(PriceRepository $priceRepository, AvailabilityRepository $availabilityRepository)
    {
        $this->priceRepository = $priceRepository;
        $this->availabilityRepository = $availabilityRepository;
    }

    public function parseAvailableStayPrices(string $propertyId, string $fromDate, string $toDate): array
    {
        // Get available property options inside time period
        $availabilities = $this->availabilityRepository->getPropertyAvailabilitiesByPeriod(
            $propertyId,
            $fromDate,
            $toDate
        );

        $result = [];
        // Parsing available property options for prices
        $availabilities->each(function (AvailabilityModel $availability) use ($propertyId, &$result) {
            if ($availability->arrival_allowed) {
                // Generating date option prices array according to number of people
                for ($persons = 1; $persons <= $this->numberOfPersons; $persons++) {
                    $personData = [];

                    // Retrieving property prices data in date range
                    $prices = $this->priceRepository->getPricesData(
                        $propertyId,
                        $availability->date,
                        $availability->date->dayOfWeek,
                        $persons
                    );

                    // Generating day options array
                    for ($day = 1; $day <= $this->numberOfDays; $day++) {
                        // Retrieving minimum prices amount per day and per duration
                        $minOneDayPrice = $prices->where('minimum_stay', '<=', $day)
                            ->min('amount');

                        $minOneDayModel = $prices->where('amount', $minOneDayPrice)->first();

                        $minMultipleDaysPrice = $prices->whereBetween('duration', [2, $day])
                            ->min('amount');

                        // Calculating direct day price amount
                        $personData[$day] = $this->calculateDayPrice(
                            $availability,
                            $prices,
                            $day,
                            $persons,
                            $minOneDayPrice,
                            $minOneDayModel,
                            $minMultipleDaysPrice
                        );
                    }
                    // Preparing response array
                    $result[$availability->date->format('Y-m-d')][$persons] = $personData;
                }
            } else {
                // Preparing empty response array
                for ($persons = 1; $persons <= $this->numberOfPersons; $persons++) {
                    $personData = [];

                    for ($day = 1; $day <= $this->numberOfDays; $day++) {
                        $personData[$day] = 0;
                    }

                    $result[$availability->date->format('Y-m-d')][$persons] = $personData;
                }
            }
        });

        return $result;
    }

    private function calculateDayPrice(
        AvailabilityModel $availability,
        Collection $prices,
        int $day,
        int $persons,
        ?int $minOneDayPrice,
        ?PriceModel $priceModel,
        ?int $minMultipleDaysPrice
    ): int {
        // Checking if price property and price are available to book
        if ($priceModel && $day <= $availability->maximum_stay && $day >= $availability->minimum_stay) {
            // Checking if there is a minimum days duration price
            if ($minMultipleDaysPrice) {
                // Calculating according to settings
                $multipleDayModel = $prices->where('amount', $minMultipleDaysPrice)->first();
                $extraDays = $day - $multipleDayModel->duration;

                if (str_starts_with($priceModel->persons, $persons)) {
                    return ($minMultipleDaysPrice + $extraDays * $minOneDayPrice) / 100;
                } else {
                    return ($minMultipleDaysPrice + $extraDays * $minOneDayPrice
                            + $priceModel->extra_person_price * $day) / 100;
                }
            } else {
                if (str_starts_with($priceModel->persons, $persons)) {
                    return $minOneDayPrice * $day / 100;
                } else {
                    return ($minOneDayPrice * $day + $priceModel->extra_person_price * $day) / 100;
                }
            }
        } else {
            return 0;
        }
    }
}
