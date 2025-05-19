<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class PresentFactory extends Factory
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
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'store_id' => Store::inRandomOrder()->first()->id,
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'status' => $this->faker->randomElement(['Telat', 'Diterima', 'Tidak Absensi', 'Libur']),
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 week'),
            'device_info' => $this->faker->userAgent,
            'check_in_ip' => $this->faker->ipv4,
            'check_out_ip' => $this->faker->ipv4,
            'check_in_time' => $this->faker->time(),
            'check_out_time' => $this->faker->time(),
        ];
    }
}
