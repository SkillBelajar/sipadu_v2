<?php

namespace Database\Factories;

use App\Models\Layanan;
use App\Models\SyaratLayanan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SyaratLayananFactory extends Factory
{
    protected $model = SyaratLayanan::class;

    public function definition(): array
    {
        return [
            'layanan_id' => Layanan::factory(),
            'nama_syarat' => fake()->word(),
            'tipe_file' => 'pdf',
        ];
    }
}
