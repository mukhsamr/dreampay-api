<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TopupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nota' => fake()->unique()->numerify('########'),
            'pengirim' => rand(1, 3),
            'penerima' => User::where('tipe', 'B')->get()->random()->id,
            'nominal' => rand(10, 100) * 10000,
        ];
    }
}
