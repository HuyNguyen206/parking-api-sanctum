<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_request_without_token_or_with_invalid_token_can_not_get_profile_endpoint(): void
    {
        $user = User::factory()->create();
        $this->getJson(route('profiles.show', $user->id), ['Authorization' => "Bearer non_valid_for_sure"])
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);

        $this->getJson(route('profiles.show', $user->id))
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_request_with_valid_token_can_get_profile_endpoint()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson(route('profiles.show', $user->id))
            ->assertStatus(200)
            ->assertJson([
                'email' => $user->email,
                'name' => $user->name
            ]);
    }

    public function test_can_update_profile()
    {
        $user = User::factory()->create(['name' => 'huy', 'email' => 'huy@gmail.com']);

        $this->actingAs($user)->putJson(route('profiles.update'), [
          'name' => 'nga',
            'email' => 'nga@gmail.com'
        ])
            ->assertStatus(200)
            ->assertJson([
                'email' => 'nga@gmail.com',
                'name' => 'nga'
            ]);

        $this->assertEquals($user->fresh()->name, 'nga');
        $this->assertEquals($user->fresh()->email, 'nga@gmail.com');
    }

    public function test_can_change_password()
    {
        $user = User::factory()->create(['name' => 'huy', 'email' => 'huy@gmail.com']);
        self::assertTrue(Hash::check('password', $user->password));

        $this->actingAs($user)->putJson(route('profiles.change-password'), [
            'current_password' => 'password',
            'password' => '123@abcdef',
            'password_confirmation' => '123@abcdef'
        ])
            ->assertStatus(200);

        self::assertTrue(Hash::check('123@abcdef', $user->fresh()->password));
    }
}
