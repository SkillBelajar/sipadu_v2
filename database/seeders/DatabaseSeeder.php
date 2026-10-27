<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Kepala Bidang',
            'email' => 'kabid@amki.ac.id',
            'role' => 'kabid',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Verifikator',
            'email' => 'verifikator@amki.ac.id',
            'role' => 'verifikator',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@amki.ac.id',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Sekolah',
            'email' => 'sekolah@amki.ac.id',
            'role' => 'sekolah',
            'password' => bcrypt('password'),
        ]);
    }
}
