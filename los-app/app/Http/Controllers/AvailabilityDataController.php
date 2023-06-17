<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Helpers\Services\AvailabilityService;
use App\Http\Requests\GetAvailabilityListRequest;

class AvailabilityDataController extends Controller
{
    /**
     * @param GetAvailabilityListRequest $request
     * @param AvailabilityService $availabilityService
     * @return View
     */
    public function getData(
        GetAvailabilityListRequest $request,
        AvailabilityService $availabilityService
    ): View {
        // $propertyId should be passed from frontend also
        $propertyId = $request->get('propertyId', '71438849-47cb-4b00-82de-34fff691f017');

        if ($request->has('fromDate')) {
            // Getting LOS table with prepared data if fromDate provided
            $response = $availabilityService->parseAvailableStayPrices(
                $propertyId,
                $request->get('fromDate')
            );

            return view('los-table', [
                'pricesData' => $response,
            ]);
        } else {
            // Getting base blade with fromDate form
            return view('los-table');
        }
    }
}
