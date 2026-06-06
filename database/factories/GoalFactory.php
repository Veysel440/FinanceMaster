<?php

namespace Database\Factories;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Goal>
 */
class GoalFactory extends Factory
{
    protected $model = Goal::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'title'          => $this->faker->sentence(3),
            'target_amount'  => $this->faker->randomFloat(2, 1000, 100000),
            'current_amount' => 0,
            'end_date'       => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
        ];
    }
}
