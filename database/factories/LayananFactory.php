<?php

namespace Database\Factories;

use App\Models\Layanan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayananFactory extends Factory
{
    protected $model = Layanan::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->word().' Layanan',
            'deskripsi' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
