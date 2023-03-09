<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticataionTest extends TestCase
{
    use WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_user_can_register_and_receive_valid_token(): void
    {
        $response = $this->postJson(route('register'), [
            'name' => 'huy',
            'email' => 'huy@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'access_token'
            ]
        ]);
        $user = User::first();
        self::assertEquals($user->name, 'huy');
        self::assertEquals($user->email, 'huy@gmail.com');
    }

    public function test_user_can_not_register_without_email()
    {
        $response = $this->postJson(route('register'), [
            'name' => 'huy',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(422)->assertJsonFragment([
            'email'=> ['The email field is required.']
        ]);

        $this->assertCount(0, User::all());
    }

    public function test_user_can_not_register_with_incorrect_email()
    {
        $response = $this->postJson(route('register'), [
            'name' => 'huy',
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => 'non_valid_email'
        ]);

        $response->assertStatus(422)->assertJsonFragment([
            'email'=> ['The email field must be a valid email address.']
        ]);

        $this->assertCount(0, User::all());
    }

    public function test_user_can_not_register_with_existing_email()
    {
        $this->postJson(route('register'), [
            'name' => 'huy',
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => 'huy@gmail.com'
        ]);

        $responseSecond = $this->postJson(route('register'), [
            'name' => 'huy',
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => 'huy@gmail.com'
        ]);

        $responseSecond->assertStatus(422)->assertJsonFragment([
            'email'=> ['The email has already been taken.']
        ]);

        $this->assertCount(1, User::all());
    }

    public function test_user_can_login_with_correct_credential()
    {
        $user = User::factory()->create();

        $this->postJson(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ])->assertSuccessful();
    }

    public function test_user_can_not_login_with_incorrect_credential()
    {
        User::factory()->create([
            'email' => 'test@gmail.com',
            'password' => bcrypt('password')
        ]);

        $this->postJson(route('login'), [
            'email' => 'huy2@gmail.com',
            'password' => 'password'
        ])->assertStatus(500);


        $this->postJson(route('login'), [
            'email' => 'huy@gmail.com',
            'password' => 'passwor'
        ])->assertStatus(500);

        $this->postJson(route('login'), [
            'password' => 'passwor'
        ])->assertStatus(422);

    }
}
