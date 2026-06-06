<?php

namespace Tests\Feature\Security;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cross-user resource access tests.
 *
 * Current behaviour: service layer returns null for non-owner; controllers
 * map that to 404 on show, 400 on update/delete. PR-09 will harden this to
 * a proper 403 via Policy classes — these tests will be updated then.
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_view_another_users_budget(): void
    {
        $user1    = User::factory()->create();
        $user2    = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user2->id]);
        $budget   = Budget::factory()->create([
            'user_id'     => $user2->id,
            'category_id' => $category->id,
        ]);

        $this->withToken($user1->createToken('t')->plainTextToken)
             ->getJson("/api/budgets/{$budget->id}")
             ->assertStatus(404);
    }

    public function test_cannot_update_another_users_goal(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $goal  = Goal::factory()->create(['user_id' => $user2->id]);

        $response = $this->withToken($user1->createToken('t')->plainTextToken)
                         ->putJson("/api/goals/{$goal->id}", [
                             'title'         => 'hack',
                             'target_amount' => 99999,
                             'end_date'      => '2030-01-01',
                         ]);

        // GoalService.updateGoal returns false → controller returns 400
        $response->assertStatus(400);
    }

    public function test_cannot_delete_another_users_category(): void
    {
        $user1    = User::factory()->create();
        $user2    = User::factory()->create();
        $category = Category::factory()->create([
            'user_id'    => $user2->id,
            'is_default' => false,
        ]);

        // CategoryService.deleteCategory → getCategory returns null for non-owner
        // → service returns false → controller returns 400
        $this->withToken($user1->createToken('t')->plainTextToken)
             ->deleteJson("/api/categories/{$category->id}")
             ->assertStatus(400);
    }

    public function test_cannot_delete_default_category(): void
    {
        $user            = User::factory()->create();
        $defaultCategory = Category::factory()->default()->create();

        // Even the user themselves cannot delete a default category
        // (CategoryService blocks is_default categories)
        $this->withToken($user->createToken('t')->plainTextToken)
             ->deleteJson("/api/categories/{$defaultCategory->id}")
             ->assertStatus(400);
    }
}
