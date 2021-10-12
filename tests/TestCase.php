<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker, LazilyRefreshDatabase;

    public function userLogin()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }
}
