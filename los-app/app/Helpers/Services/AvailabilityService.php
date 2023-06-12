<?php

namespace App\Helpers\Services;

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
        $availabilities = $this->availabilityRepository->getPropertyAvailabilitiesByPeriod(
            $propertyId,
            $fromDate,
            $toDate
        );

        $result = [];

        $availabilities->each(function (AvailabilityModel $availability) use ($propertyId, &$result) {
            if ($availability->arrival_allowed) {
                for ($persons = 1; $persons <= $this->numberOfPersons; $persons++) {
                    $personData = [];

                    $prices = $this->priceRepository->getPricesData(
                        $propertyId,
                        $availability->date,
                        $availability->date->dayOfWeek,
                        $persons
                    );

                    for ($day = 1; $day <= $this->numberOfDays; $day++) {
                        $minOneDayPrice = $prices->where('minimum_stay', '<=', $day)
                            ->min('amount');

                        $minOneDayModel = $prices->where('amount', $minOneDayPrice)->first();

                        $minMultipleDaysPrice = $prices->whereBetween('duration', [2, $day])
                            ->min('amount');

                        $personData[$day] = $this->calculateDayPrice(
                            $prices,
                            $day,
                            $minOneDayModel->persons,
                            $persons,
                            $minOneDayPrice,
                            $minOneDayModel->extra_person_price,
                            $minMultipleDaysPrice
                        );
                    }

                    $result[$availability->date->format('Y-m-d')][$persons] = $personData;
                }
            } else {
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
        Collection $prices,
        int $day,
        string $allowedPersons,
        int $persons,
        int $minOneDayPrice,
        int $minOneDayExtraPrice,
        ?int $minMultipleDaysPrice
    ): int {
        if ($minMultipleDaysPrice) {
            $multipleDayModel = $prices->where('amount', $minMultipleDaysPrice)->first();
            $extraDays = $day - $multipleDayModel->duration;

            if (str_starts_with($allowedPersons, $persons)) {
                return ($minMultipleDaysPrice + $extraDays * $minOneDayPrice) / 100;
            } else {
                return ($minMultipleDaysPrice + $extraDays * $minOneDayPrice + $minOneDayExtraPrice * $day) / 100;
            }
        } else {
            if (str_starts_with($allowedPersons, $persons)) {
                return $minOneDayPrice * $day / 100;
            } else {
                return ($minOneDayPrice * $day + $minOneDayExtraPrice * $day) / 100;
            }
        }
    }
}
