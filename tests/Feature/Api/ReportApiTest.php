<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_requires_authentication(): void
    {
        $this->getJson('/api/reports/summary')->assertStatus(401);
    }

    public function test_summary_returns_correct_totals(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        Transaction::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'type'        => 'income',
            'amount'      => 3000,
            'date'        => now()->format('Y-m-d'),
        ]);
        Transaction::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'type'        => 'expense',
            'amount'      => 1200,
            'date'        => now()->format('Y-m-d'),
        ]);

        $token = $user->createToken('t')->plainTextToken;

        $response = $this->withToken($token)
                         ->getJson('/api/reports/summary?period=monthly');

        $response->assertStatus(200);
        $this->assertEquals(3000, $response->json('data.income'));
        $this->assertEquals(1200, $response->json('data.expense'));
        $this->assertEquals(1800, $response->json('data.balance'));
    }

    public function test_date_filter_excludes_out_of_range_transactions(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        Transaction::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'type'        => 'expense',
            'amount'      => 500,
            'date'        => '2025-05-15',
        ]);
        // out of range
        Transaction::factory()->create([
            'user_id'     => $user->id,
            'category_id' => $category->id,
            'type'        => 'expense',
            'amount'      => 999,
            'date'        => '2025-06-01',
        ]);

        $token = $user->createToken('t')->plainTextToken;

        $response = $this->withToken($token)
                         ->getJson('/api/reports/summary?period=custom&start_date=2025-05-01&end_date=2025-05-31');

        $response->assertStatus(200);
        $this->assertEquals(500, $response->json('data.expense'));
    }
}
