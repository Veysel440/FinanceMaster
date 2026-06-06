<?php

namespace Tests\Unit;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BudgetLimitExceededNotification;
use App\Services\BudgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BudgetServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_transaction_is_a_noop(): void
    {
        Notification::fake();

        $user        = User::factory()->create();
        $transaction = Transaction::factory()->income()->make([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);
        app(BudgetService::class)->checkBudgetsForTransaction($user->id, $transaction);

        Notification::assertNothingSent();
    }

    public function test_exceeded_budget_sends_notification(): void
    {
        Notification::fake();

        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        // 1000 TL bütçe
        Budget::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 1000,
            'month'       => now()->startOfMonth()->format('Y-m-d'),
        ]);

        // Toplam 1200 TL harcama -> 200 TL aşım
        Transaction::factory()->count(12)->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'type'        => 'expense',
            'amount'      => 100,
            'date'        => now()->format('Y-m-d'),
        ]);

        $newTx = Transaction::factory()->expense()->make([
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user);
        app(BudgetService::class)->checkBudgetsForTransaction($user->id, $newTx);

        Notification::assertSentTo($user, BudgetLimitExceededNotification::class);
    }

    public function test_within_budget_does_not_send_notification(): void
    {
        Notification::fake();

        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        Budget::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'amount'      => 5000,
            'month'       => now()->startOfMonth()->format('Y-m-d'),
        ]);

        Transaction::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'type'        => 'expense',
            'amount'      => 100,
            'date'        => now()->format('Y-m-d'),
        ]);

        $newTx = Transaction::factory()->expense()->make([
            'user_id'     => $user->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user);
        app(BudgetService::class)->checkBudgetsForTransaction($user->id, $newTx);

        Notification::assertNothingSent();
    }
}
