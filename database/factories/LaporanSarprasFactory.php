<?php

namespace Database\Factories;

use App\Models\LaporanSarpras;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaporanSarprasFactory extends Factory
{
    protected $model = LaporanSarpras::class;

    public function definition(): array
    {
        return [
            'nama_ruangan' => fake()->word().' Ruangan',
            'kondisi' => 'baik',
            'foto_path' => 'laporan_sarpras/fake.jpg',
            'status_verifikasi' => 'pending',
        ];
    }
}
