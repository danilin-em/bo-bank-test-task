<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_account_id' => null,
            'to_account_id' => \App\Models\Account::factory(),
            'amount' => $this->faker->numberBetween(1000, 100000),
            'type' => $this->faker->randomElement(['deposit', 'transfer']),
            'status' => 'completed',
            'reference_id' => $this->faker->unique()->regexify('[A-Z0-9]{12}'),
        ];
    }
}
