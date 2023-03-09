<?php

namespace Tests\Feature;

use App\Models\Zone;
use Tests\TestCase;

class ZoneTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_publish_user_can_get_all_zones(): void
    {
        $zones = Zone::all()->toArray();

       $res = $this->getJson(route('zones.index'));
       $res->assertStatus(200);

       foreach ($res->json()['data']['data'] as $zone) {
           $this->assertContains($zone['name'], array_column($zones, 'name'));
           $this->assertContains($zone['price_per_hour'], array_column($zones, 'price_per_hour'));
       }
    }
}
