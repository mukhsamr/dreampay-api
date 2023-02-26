<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nota' => fake()->unique()->numerify('########'),
            'pengirim' => User::where('tipe', 'B')->get()->random()->id,
            'penerima' => User::where('tipe', 'S')->get()->random()->id,
            'nominal' => rand(1, 100) * 1000,
        ];
    }
}
