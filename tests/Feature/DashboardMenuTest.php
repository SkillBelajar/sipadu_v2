<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sekolah dashboard shows role menu', function () {
    $sekolah = User::factory()->create([
        'email' => 'sekolah@amki.ac.id',
        'role' => 'sekolah',
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($sekolah)->get('/sekolah/dashboard');

    expect($response->status())->toBe(200);
    $response->assertSee('Sekolah Dashboard');
    $response->assertSee('Ajukan Layanan');
    $response->assertSee('Laporan Sarpras');
    $response->assertSee('Profile');
    $response->assertSee('Logout');
});

it('kabid dashboard shows role menu', function () {
    $kabid = User::factory()->create([
        'email' => 'kabid@amki.ac.id',
        'role' => 'kabid',
        'password' => bcrypt('password'),
    ]);

    $response = $this->actingAs($kabid)->get('/kabid/dashboard');

    expect($response->status())->toBe(200);
    $response->assertSee('Kabid Dashboard');
    $response->assertSee('Pantau Semua Pengajuan');
    $response->assertSee('Rekapitulasi');
    $response->assertSee('Verifikasi Sarpras');
    $response->assertSee('Profile');
    $response->assertSee('Logout');
});
