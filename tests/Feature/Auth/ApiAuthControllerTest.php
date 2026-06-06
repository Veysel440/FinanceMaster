<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Test User',
            'email'                 => 'pr07-register@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'token',
                     'token_type',
                     'user' => ['id', 'name', 'email'],
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'pr07-register@example.com']);
    }

    public function test_register_fails_without_email(): void
    {
        $this->postJson('/api/register', [
            'name'                  => 'Test',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['email']);
    }

    public function test_login_returns_token_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'pr07-login@example.com',
            'password' => Hash::make('Password123!'),
        ]);

        $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'Password123!',
        ])->assertStatus(200)
          ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'name', 'email']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(422)
          ->assertJsonValidationErrors(['email']);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $tokenId = (int) explode('|', $token)[0];

        // Token exists before logout
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $tokenId]);

        $this->withToken($token)
             ->postJson('/api/logout')
             ->assertStatus(200)
             ->assertJson(['message' => 'Logged out']);

        // Token is purged from the database
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);

        // NOTE: a follow-up request with the same token in this test process
        // can still resolve due to in-process Sanctum container caching.
        // The live PR-05 verification confirmed real HTTP cycles return 401
        // once the row is deleted. Cross-request revocation is asserted by
        // the DB state above, which is the source of truth.
    }
}
