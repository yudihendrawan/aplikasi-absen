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
class InvoiceFactory extends Factory
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
            'store_id' => Store::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'invoice_number' => strtoupper(Str::random(10)),
            'total' => $this->faker->randomFloat(2, 100000, 1000000),
            'due_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['Belum Dibayar', 'Sebagian', 'Lunas']),
            'issued_at' => $this->faker->dateTime(),
            'description' => $this->faker->sentence,
        ];
    }
}
