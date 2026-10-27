<?php

namespace Database\Factories;

use App\Models\Layanan;
use App\Models\Pengajuan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PengajuanFactory extends Factory
{
    protected $model = Pengajuan::class;

    public function definition(): array
    {
        return [
            'layanan_id' => Layanan::factory(),
            'user_id' => \App\Models\User::factory(),
            'status' => 'pending',
        ];
    }
}
