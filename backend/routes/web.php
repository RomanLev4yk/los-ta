<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/auth', [AuthController::class, 'authUser'])->name('authUser');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/period-revenue', [AvailabilityDataController::class, 'getRevenueByPeriod'])
        ->name('getRevenueByPeriod');

    Route::get('/day-revenue', [AvailabilityDataController::class, 'getRevenueByDay'])
        ->name('getRevenueByDay');
});
