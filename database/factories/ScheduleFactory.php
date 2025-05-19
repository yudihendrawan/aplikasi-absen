<?php

namespace Database\Factories;

use App\Models\Present;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ScheduleFactory extends Factory
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
        $date = $this->faker->dateTimeBetween('now', '+1 week');
        $checkIn = $this->faker->time('H:i:s');
        $checkOut = $this->faker->time('H:i:s');

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'store_id' => Store::inRandomOrder()->first()->id,
            'presents_id' => Present::inRandomOrder()->first()->id ?? null,
            'created_by' => User::first()->id,
            'date' => $date,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'time_tolerance' => '00:10:00',
        ];
    }
}
