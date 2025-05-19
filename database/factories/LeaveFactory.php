<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class LeaveFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 week', '+1 week');
        $end = (clone $start)->modify('+1 day');

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'name' => $this->faker->word,
            'reason' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_date' => $start,
            'end_date' => $end,
        ];
    }
}
