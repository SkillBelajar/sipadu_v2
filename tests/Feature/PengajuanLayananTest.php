<?php

use App\Models\DokumenPengajuan;
use App\Models\Layanan;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('sekolah can submit pengajuan with pdf files', function () {
    Storage::fake('public');

    $sekolah = User::factory()->create(['role' => 'sekolah']);
    $layanan = Layanan::factory()->create(['nama' => 'SKH']);
    $layanan->syarat()->create(['nama_syarat' => 'KTP', 'tipe_file' => 'pdf']);
    $layanan->syarat()->create(['nama_syarat' => 'KK', 'tipe_file' => 'pdf']);

    $payload = [
        'layanan_id' => $layanan->id,
        'files' => [
            UploadedFile::fake()->create('ktp.pdf', 1024, 'application/pdf'),
            UploadedFile::fake()->create('kk.pdf', 1024, 'application/pdf'),
        ],
    ];

    $response = $this->actingAs($sekolah)->post('/sekolah/pengajuan', $payload);

    expect($response->status())->toBe(201);
    expect(Pengajuan::count())->toBe(1);
    expect(Pengajuan::first()->status)->toBe('pending');
    expect(DokumenPengajuan::count())->toBe(2);
    Storage::disk('public')->assertExists(DokumenPengajuan::first()->file_path);
});

it('non-pdf file returns validation error', function () {
    $sekolah = User::factory()->create(['role' => 'sekolah']);
    $layanan = Layanan::factory()->create();
    $layanan->syarat()->create(['nama_syarat' => 'KTP', 'tipe_file' => 'pdf']);

    $payload = [
        'layanan_id' => $layanan->id,
        'files' => [
            UploadedFile::fake()->create('dokumen.jpg', 1024, 'image/jpeg'),
        ],
    ];

    $response = $this->actingAs($sekolah)->post('/sekolah/pengajuan', $payload);

    expect($response->status())->toBe(302);
    $response->assertSessionHasErrors('files.*');
});

it('admin cannot submit pengajuan', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $layanan = Layanan::factory()->create();

    $payload = [
        'layanan_id' => $layanan->id,
        'files' => [
            UploadedFile::fake()->create('ktp.pdf', 1024, 'application/pdf'),
        ],
    ];

    $response = $this->actingAs($admin)->post('/sekolah/pengajuan', $payload);

    expect($response->status())->toBe(403);
});

it('guest cannot submit pengajuan', function () {
    $layanan = Layanan::factory()->create();

    $payload = [
        'layanan_id' => $layanan->id,
        'files' => [
            UploadedFile::fake()->create('ktp.pdf', 1024, 'application/pdf'),
        ],
    ];

    $response = $this->post('/sekolah/pengajuan', $payload);

    expect($response->status())->toBe(302);
});
