<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_login_returns_access_token_for_active_user(): void
    {
        $user = User::factory()->create([
            'name' => 'API Admin',
            'phone' => '09911111111',
            'password' => 'secret123',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'phone' => $user->phone,
            'password' => 'secret123',
            'device_name' => 'android-test-device',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.phone', $user->phone)
            ->assertJsonPath('user.role', 'admin');

        $this->assertIsString($response->json('access_token'));
        $this->assertNotEmpty($response->json('access_token'));
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }
}
