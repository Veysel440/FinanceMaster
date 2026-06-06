<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingWithToken(User $user): self
    {
        return $this->withToken($user->createToken('test')->plainTextToken);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/transactions')->assertStatus(401);
    }

    public function test_store_transaction_returns_201(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->actingWithToken($user)
             ->postJson('/api/transactions', [
                 'type'        => 'expense',
                 'amount'      => 150.50,
                 'category_id' => $category->id,
                 'date'        => '2025-06-01',
                 'description' => 'Market alışverişi',
             ])
             ->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount'  => 150.50,
            'type'    => 'expense',
        ]);
    }

    public function test_negative_amount_fails_validation(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->actingWithToken($user)
             ->postJson('/api/transactions', [
                 'type'        => 'expense',
                 'amount'      => -50,
                 'category_id' => $category->id,
                 'date'        => '2025-06-01',
             ])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['amount']);
    }

    public function test_idor_another_users_category_id_returns_422(): void
    {
        $user1           = User::factory()->create();
        $user2           = User::factory()->create();
        $categoryOfUser2 = Category::factory()->create([
            'user_id'    => $user2->id,
            'is_default' => false,
        ]);

        $this->actingWithToken($user1)
             ->postJson('/api/transactions', [
                 'type'        => 'expense',
                 'amount'      => 100,
                 'category_id' => $categoryOfUser2->id,
                 'date'        => '2025-06-01',
             ])
             ->assertStatus(422)
             ->assertJsonValidationErrors(['category_id']);
    }

    public function test_cannot_view_another_users_transaction(): void
    {
        $user1    = User::factory()->create();
        $user2    = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user2->id]);
        $tx       = Transaction::factory()->create([
            'user_id'     => $user2->id,
            'category_id' => $category->id,
        ]);

        // Service returns null for non-owner, controller maps to 404
        // (PR-09 will harden this to 403 via Policy)
        $this->actingWithToken($user1)
             ->getJson("/api/transactions/{$tx->id}")
             ->assertStatus(404);
    }

    public function test_default_category_is_usable_by_any_user(): void
    {
        $user            = User::factory()->create();
        $defaultCategory = Category::factory()->default()->create();

        $this->actingWithToken($user)
             ->postJson('/api/transactions', [
                 'type'        => 'income',
                 'amount'      => 3000,
                 'category_id' => $defaultCategory->id,
                 'date'        => '2025-06-01',
             ])
             ->assertStatus(201);
    }
}
