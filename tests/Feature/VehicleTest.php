<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_user_only_get_their_own_vehicle(): void
    {
        $user = User::factory()->create();
        $user->vehicles()->saveMany(Vehicle::factory(2)->make());

        $vehicles = Vehicle::factory(3)->create();
        $res = $this->actingAs($user)->getJson(route('vehicles.index'));
        $res->assertJsonStructure([
            'data' => [
                'data'
            ],
            'message'
        ])
            ->assertJsonCount(2, 'data.data')
            ->assertJsonPath('data.data.0.plate_number', $user->fresh()->vehicles->first()->plate_number)
            ->assertJsonMissing($vehicles->first()->toArray())
            ->assertJsonMissing($vehicles->get(1)->toArray())
            ->assertJsonMissing($vehicles->last()->toArray());

        $data = $res->json()['data']['data'];
        self::assertCount(2, $data);
    }

    public function test_user_can_create_vehicle()
    {
      $this->actingAs($user = User::factory()->create())->postJson(route('vehicles.store'), Vehicle::factory()->raw(['user_id' => $user->id]))
      ->assertSuccessful();

      self::assertEquals(Vehicle::first()->user_id, $user->id);
    }

    public function test_user_can_show_vehicle()
    {
        $vehicle = Vehicle::factory()->create();

        $this->actingAs($vehicle->user)->getJson(route('vehicles.show', $vehicle->id))
            ->assertSuccessful()
            ->assertJsonPath('data.plate_number', $vehicle->plate_number);
    }

    public function test_user_can_update_vehicle()
    {
        $vehicle = Vehicle::factory()->create(['plate_number' => '123']);

        $this->actingAs($vehicle->user)->putJson(route('vehicles.update', $vehicle->id), [
            'plate_number' => '456'
        ])
            ->assertSuccessful();

        $this->assertDatabaseHas('vehicles', [
            'user_id' => $vehicle->user->id,
            'plate_number' => '456'
        ]);
    }

    public function test_can_delete_vehicle()
    {
        $vehicle = Vehicle::factory()->create(['plate_number' => '123']);

        $this->actingAs($user = $vehicle->user)->deleteJson(route('vehicles.destroy', $vehicle->id))
            ->assertSuccessful();

        $this->assertDatabaseMissing('vehicles', [
            'user_id' => $vehicle->user->id,
            'plate_number' => '123'
        ]);

        $this->assertCount(0, $user->fresh()->vehicles);
    }

}
