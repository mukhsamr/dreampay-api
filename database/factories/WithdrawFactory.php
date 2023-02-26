<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nota' => fake()->numerify('########'),
            'pengirim' => 1,
            'penerima' => User::where('tipe', 'S')->get()->random()->id,
            'nominal' => rand(1, 100) * 1000,
        ];
    }
}
