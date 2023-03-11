<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParkingResource;
use App\Models\Parking;
use App\Responsable\ResponseSuccess;
use App\Services\ParkingPriceService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Parking
 */
class ParkingController extends Controller
{
    public function start(Request $request)
    {
        $validated = $request->validate([
           'vehicle_id' => ['required', 'integer', 'numeric', Rule::exists('vehicles', 'id')],
           'zone_id' => ['required', 'integer', 'numeric', Rule::exists('zones', 'id')] ,
        ]);
        if (Parking::query()->where('vehicle_id', $validated['vehicle_id'])->whereNull('stop_time')->exists()) {
            return response()->json([
                'message' => "Can't parking twice for same vehicle. Please stop the current active parking",
                'errors' => [
                    'general' => ["Can't parking twice for same vehicle. Please stop the current active parking"]
                ]
            ], 422);
        }

        $parking = $request->user()->parkings()->create($validated);

        return new ResponseSuccess(ParkingResource::make($parking->load(['vehicle', 'zone'])));
    }

    public function stop(Request $request, Parking $parking)
    {
        $parking->update([
            'stop_time' => now(),
            'total_price' => ParkingPriceService::calculatePrice($parking->zone->price_per_hour, $parking->start_time)
        ]);

        return new ResponseSuccess(ParkingResource::make($parking));
    }

    public function show(Parking $parking)
    {
        return new ResponseSuccess(ParkingResource::make($parking));
    }
}
