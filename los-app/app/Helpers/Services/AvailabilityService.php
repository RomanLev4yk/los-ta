<?php

namespace App\Helpers\Services;

use Carbon\Carbon;
use App\Models\PriceModel;
use App\Enum\DateFormatEnum;
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

    public function parseAvailableStayPrices(string $propertyId, string $fromDate): array
    {
        $toDate = Carbon::parse($fromDate)->addDays($this->numberOfDays)->format(DateFormatEnum::DATE);

        // Get available property options inside time period
        $availabilities = $this->availabilityRepository->getPropertyAvailabilitiesByPeriod(
            $propertyId,
            $fromDate,
            $toDate
        );

        // Retrieving property prices data in date range
        $rangePrices = $this->priceRepository->getRangePricesData(
            $propertyId,
            $fromDate,
            Carbon::parse($toDate)->addDays($this->numberOfDays)->format(DateFormatEnum::DATE)
        );

        $result = [];
        // Parsing available property options for prices
        $availabilities->each(function (AvailabilityModel $availability) use ($propertyId, &$result, $rangePrices) {
            $date = $availability->date->format('Y-m-d');

            if ($availability->arrival_allowed) {
                // Generating date option prices array according to number of people
                for ($persons = 1; $persons <= $this->numberOfPersons; $persons++) {
                    // Preparing persons / days response data
                    $personData = [];

                    // Retrieving property prices data in date range
                    $prices = $rangePrices->filter(function (PriceModel $priceItem) use (
                        $availability,
                        $persons,
                        $date
                    ) {
                        return $priceItem->period_from <= $date
                            && $priceItem->period_till >= $date
                            && str_contains($priceItem->weekdays, $availability->date->dayOfWeek)
                            && str_contains($priceItem->persons, $persons);
                    });

                    // Generating day options array
                    for ($day = 1, $addDays = 0; $day <= $this->numberOfDays; $day++, $addDays++) {
                        // Retrieving minimum prices amount per day and per duration
                        $minOneDayPrice = $prices->where('minimum_stay', '<=', $day)
                            ->min('amount');

                        $minOneDayModel = $prices->where('amount', $minOneDayPrice)->first();

                        $minMultipleDaysPrice = $prices->whereBetween('duration', [2, $day])
                            ->min('amount');

                        // Checking if price exists for further date
                        $priceExist = $rangePrices->filter(function (PriceModel $priceItem) use (
                            $availability,
                            $persons,
                            $addDays
                        ) {
                            return $priceItem->period_from <= $availability->date->addDays($addDays)
                                    ->format(DateFormatEnum::DATE)
                                && $priceItem->period_till >= $availability->date->addDays($addDays)
                                    ->format(DateFormatEnum::DATE)
                                && str_contains($priceItem->weekdays, $availability->date->addDays($addDays)->dayOfWeek)
                                && str_contains($priceItem->persons, $persons);
                        });

                        if ($priceExist->isNotEmpty()) {
                            // Calculating direct day price amount if price exists
                            $personData[$day] = $this->calculateDayPrice(
                                $availability,
                                $prices,
                                $day,
                                $persons,
                                $minOneDayPrice,
                                $minOneDayModel,
                                $minMultipleDaysPrice
                            );
                        } else {
                            // Setting not available null price value if price for this date doesnt exist
                            $personData[$day] = 0;
                        }
                    }
                    // Preparing response array
                    $result[$date][$persons] = $personData;
                }
            } else {
                // Preparing empty response array
                $result[$date] = $this->makeNotAvailableArray();
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
            if ($minMultipleDaysPrice) {
                // Calculating if there is a minimum multiple days duration price
                $multipleDayModel = $prices->where('amount', $minMultipleDaysPrice)->first();
                $extraDays = $day - $multipleDayModel->duration;

                if (str_starts_with($priceModel->persons, $persons)) {
                    // Calculate price per base number of persons
                    return ($minMultipleDaysPrice + $extraDays * $minOneDayPrice) / 100;
                }
                // Calculate price per extra number of persons
                return ($minMultipleDaysPrice + $extraDays * $minOneDayPrice
                        + $priceModel->extra_person_price * $day) / 100;
            } else {
                // Calculating if there is a base one day duration price
                if (str_starts_with($priceModel->persons, $persons)) {
                    // Calculate price per base number of persons
                    return $minOneDayPrice * $day / 100;
                }
                // Calculate price per extra number of persons
                return ($minOneDayPrice * $day + $priceModel->extra_person_price * $day) / 100;
            }
        } else {
            return 0;
        }
    }

    private function makeNotAvailableArray(): array
    {
        // Preparing array of not available property per persons and days
        $notAvailableArray = [];
        for ($persons = 1; $persons <= $this->numberOfPersons; $persons++) {
            $personData = [];

            for ($day = 1; $day <= $this->numberOfDays; $day++) {
                $personData[$day] = 0;
            }
            $notAvailableArray[$persons] = $personData;
        }

        return $notAvailableArray;
    }
}
