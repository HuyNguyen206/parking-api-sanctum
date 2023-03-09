<?php

namespace App\Services;

use Carbon\Carbon;

class ParkingPriceService
{
    public static array $data;
    public static function calculatePrice(float $price, Carbon $startTime, Carbon|null $endTime = null)
    {
        $endTime = $endTime ?? now();

        return number_format($startTime->diffInMinutes($endTime) * $price / 60, 2);
    }
}
