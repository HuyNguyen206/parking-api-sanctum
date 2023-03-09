<?php

namespace Tests\Feature;

use App\Models\Parking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Services\ParkingPriceService;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_can_start_parking(): void
    {
        $user = User::factory()->create();
        $vehicle = $user->vehicles()->save(Vehicle::factory()->create(['user_id' => $user->id]));

        $zone = Zone::first();

        $this->actingAs($user)->postJson(route('parkings.start'), [
            'zone_id' => $zone->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $this->assertDatabaseHas('parkings', [
            'user_id' => $user->id,
            'zone_id' => $zone->id,
            'vehicle_id' => $vehicle->id
        ]);
    }

    public function test_can_show_the_parking_detail_with_total_price_at_the_current_time()
    {
        [$parkingId, $user, $zone] = $this->prepare();

        $this->travel(5)->hours();

        $parking = Parking::find($parkingId);

        $this->actingAs($user)->getJson(route('parkings.show', $parkingId))
        ->assertJsonPath('data.total_price', ParkingPriceService::calculatePrice($zone->price_per_hour, $parking->start_time));
    }

    public function test_can_update_the_parking_detail_with_total_price_at_the_current_time()
    {
        [$parkingId, $user, $zone] = $this->prepare();

        $this->travel(7)->hours();

        $parking = Parking::find($parkingId);

        $this->actingAs($user)->putJson(route('parkings.stop', $parkingId))
        ->assertJsonPath('data.total_price', ParkingPriceService::calculatePrice($zone->price_per_hour, $parking->start_time));
    }

    private function prepare()
    {
        $user = User::factory()->create();
        $vehicle = $user->vehicles()->save(Vehicle::factory()->create(['user_id' => $user->id]));

        $zone = Zone::first();

        $res = $this->actingAs($user)->postJson(route('parkings.start'), [
            'zone_id' => $zone->id,
            'vehicle_id' => $vehicle->id,
        ]);
        return [$res->json()['data']['id'], $user, $zone];
    }
}
