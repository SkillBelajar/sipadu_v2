<?php

namespace Database\Factories;

use App\Models\DokumenPengajuan;
use App\Models\SyaratLayanan;
use Illuminate\Database\Eloquent\Factories\Factory;

class DokumenPengajuanFactory extends Factory
{
    protected $model = DokumenPengajuan::class;

    public function definition(): array
    {
        return [
            'status' => 'pending',
            'catatan_verifikator' => null,
            'file_path' => 'dokumen_pengajuan/fake.pdf',
            'syarat_id' => SyaratLayanan::factory(),
        ];
    }
}
