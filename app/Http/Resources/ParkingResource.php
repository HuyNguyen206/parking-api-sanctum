<?php

namespace App\Http\Resources;

use App\Services\ParkingPriceService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalPrice = $this->total_price ?? ParkingPriceService::calculatePrice($this->zone->price_per_hour, $this->start_time);

        return [
            'id' => $this->id,
            'zone' => ZoneResource::make($this->whenLoaded('zone')),
            'vehicle' => VehicleResource::make($this->whenLoaded('vehicle')),
            'start_time' => $this->start_time->toDateTimeString(),
            'stop_time' => $this->stop_time?->toDateTimeString(),
            'total_price' => $totalPrice
        ];
    }
}
