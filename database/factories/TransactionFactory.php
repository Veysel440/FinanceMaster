<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::factory(),
            'type'        => $this->faker->randomElement(['income', 'expense']),
            'amount'      => $this->faker->randomFloat(2, 10, 5000),
            'description' => $this->faker->sentence(4),
            'date'        => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }

    public function income(): static
    {
        return $this->state(fn () => ['type' => 'income']);
    }

    public function expense(): static
    {
        return $this->state(fn () => ['type' => 'expense']);
    }
}
