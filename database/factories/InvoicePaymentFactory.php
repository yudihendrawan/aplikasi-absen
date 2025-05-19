<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Present;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class InvoicePaymentFactory extends Factory
{

    protected $model = InvoicePayment::class;

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
            'invoice_id' => Invoice::inRandomOrder()->first()->id,
            'present_id' => Present::inRandomOrder()->first()->id,
            'paid_by' => $this->faker->name,
            'amount' => $this->faker->randomFloat(2, 10000, 500000),
            'paid_at' => $this->faker->dateTime(),
            'notes' => $this->faker->sentence,
        ];
    }
}
