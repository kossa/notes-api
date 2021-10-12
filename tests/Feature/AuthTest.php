<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /** @test */
    public function user_can_login_and_logout()
    {
        $this->withoutExceptionHandling();

        // Create a user
        $email = $this->faker->email;
        User::factory(['email' => $email])->create();

        // Hit url login
        $res = $this->postJson('/api/v1/login', ['email' => $email, 'password' => 'password'])
                ->assertOk()
                ->assertJson(['token_type' => 'Bearer']);

        $access_token = $res->getData()->access_token;

        $res = $this->getJson('/api/v1/user', [
                'Authorization' => "Bearer " . $access_token
            ]);

        $res->assertOk()
                ->assertSee(['id' => 1]);

        // Assert logout
        $res = $this->postJson('/api/v1/logout', [
            'Authorization' => "Bearer " . $access_token
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => 1]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /** @test */
    public function get_logged_user()
    {
        $this->userLogin();

        $this->getJson('/api/v1/user')
            // ->dump()
            ->assertOk()
            ->assertSee(['id' => 1]);
    }
}
