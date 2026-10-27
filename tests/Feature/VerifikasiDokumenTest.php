<?php

use App\Models\DokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('verifikator can verify dokumen to valid', function () {
    Storage::fake('public');

    $verifikator = User::factory()->create(['role' => 'verifikator']);
    $pengajuan = Pengajuan::factory()->create(['status' => 'pending']);
    $dokumen = DokumenPengajuan::factory()->create([
        'pengajuan_id' => $pengajuan->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($verifikator)->patch("/verifikator/dokumen/{$dokumen->id}", [
        'status' => 'valid',
        'catatan_verifikator' => 'Dokumen lengkap',
    ]);

    expect($response->status())->toBe(200);
    expect($dokumen->refresh()->status)->toBe('valid');
    expect($dokumen->refresh()->catatan_verifikator)->toBe('Dokumen lengkap');
});

it('all valid docs approve parent pengajuan to disetujui', function () {
    $verifikator = User::factory()->create(['role' => 'verifikator']);
    $pengajuan = Pengajuan::factory()->create(['status' => 'pending']);
    DokumenPengajuan::factory()->count(2)->create([
        'pengajuan_id' => $pengajuan->id,
        'status' => 'pending',
    ]);

    foreach ($pengajuan->dokumen as $dokumen) {
        $this->actingAs($verifikator)->patch("/verifikator/dokumen/{$dokumen->id}", [
            'status' => 'valid',
            'catatan_verifikator' => 'OK',
        ]);
    }

    expect($pengajuan->refresh()->status)->toBe('disetujui');
});

it('one revisi doc sets parent pengajuan to revisi', function () {
    $verifikator = User::factory()->create(['role' => 'verifikator']);
    $pengajuan = Pengajuan::factory()->create(['status' => 'pending']);
    DokumenPengajuan::factory()->count(2)->create([
        'pengajuan_id' => $pengajuan->id,
        'status' => 'pending',
    ]);

    $first = $pengajuan->dokumen->first();
    $this->actingAs($verifikator)->patch("/verifikator/dokumen/{$first->id}", [
        'status' => 'revisi',
        'catatan_verifikator' => 'Perbaiki KTP',
    ]);

    expect($pengajuan->refresh()->status)->toBe('revisi');
});

it('verification action is logged', function () {
    $verifikator = User::factory()->create(['role' => 'verifikator']);
    $pengajuan = Pengajuan::factory()->create(['status' => 'pending']);
    $dokumen = DokumenPengajuan::factory()->create([
        'pengajuan_id' => $pengajuan->id,
        'status' => 'pending',
    ]);

    $this->actingAs($verifikator)->patch("/verifikator/dokumen/{$dokumen->id}", [
        'status' => 'valid',
        'catatan_verifikator' => 'OK',
    ]);

    expect(\App\Models\ActivityLog::where('user_id', $verifikator->id)
        ->where('action', 'like', '%verifikasi%')
        ->exists())->toBeTrue();
});

it('non-verifikator roles get 403', function () {
    $sekolah = User::factory()->create(['role' => 'sekolah']);
    $admin = User::factory()->create(['role' => 'admin']);
    $pengajuan = Pengajuan::factory()->create();
    $dokumen = DokumenPengajuan::factory()->create(['pengajuan_id' => $pengajuan->id]);

    foreach ([$sekolah, $admin] as $user) {
        $response = $this->actingAs($user)->patch("/verifikator/dokumen/{$dokumen->id}", [
            'status' => 'valid',
            'catatan_verifikator' => 'OK',
        ]);
        expect($response->status())->toBe(403);
    }
});

it('guest gets redirect', function () {
    $pengajuan = Pengajuan::factory()->create();
    $dokumen = DokumenPengajuan::factory()->create(['pengajuan_id' => $pengajuan->id]);

    $response = $this->patch("/verifikator/dokumen/{$dokumen->id}", [
        'status' => 'valid',
        'catatan_verifikator' => 'OK',
    ]);

    expect($response->status())->toBe(302);
});
