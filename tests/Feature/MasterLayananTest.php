<?php

use App\Models\ActivityLog;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin can create layanan with syarat', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $payload = [
        'nama' => 'Pengajuan SKH',
        'deskripsi' => 'Layanan pengajuan surat keterangan',
        'is_active' => true,
        'syarat' => [
            ['nama_syarat' => 'KTP', 'tipe_file' => 'pdf'],
            ['nama_syarat' => 'KK', 'tipe_file' => 'pdf'],
        ],
    ];

    $response = $this->actingAs($admin)->post('/admin/layanan', $payload);

    expect($response->status())->toBe(201);
    expect(Layanan::count())->toBe(1);
    expect(Layanan::first()->syarat()->count())->toBe(2);
});

it('sekolah cannot create layanan', function () {
    $sekolah = User::factory()->create(['role' => 'sekolah']);
    $payload = [
        'nama' => 'Layanan X',
        'syarat' => [['nama_syarat' => 'Dokumen']],
    ];

    $response = $this->actingAs($sekolah)->post('/admin/layanan', $payload);

    expect($response->status())->toBe(403);
});

it('guest cannot create layanan', function () {
    $payload = [
        'nama' => 'Layanan X',
        'syarat' => [['nama_syarat' => 'Dokumen']],
    ];

    $response = $this->post('/admin/layanan', $payload);

    expect($response->status())->toBe(302);
});

it('admin can retrieve layanan list with syarat', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $layanan = Layanan::factory()->create(['nama' => 'Layanan A']);
    $layanan->syarat()->create(['nama_syarat' => 'Dokumen 1', 'tipe_file' => 'pdf']);

    $response = $this->actingAs($admin)->get('/admin/layanan');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.syarat'))->toHaveCount(1);
});
