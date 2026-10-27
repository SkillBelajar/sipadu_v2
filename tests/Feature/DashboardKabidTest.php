<?php

use App\Jobs\LogUserActivityJob;
use App\Models\LaporanSarpras;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('kabid can access pengajuan index', function () {
    $kabid = User::factory()->create(['role' => 'kabid']);

    $response = $this->actingAs($kabid)->get('/kabid/pengajuan');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toBeArray();
});

it('kabid can access sarpras recap with aggregation', function () {
    $kabid = User::factory()->create(['role' => 'kabid']);
    $sekolah = User::factory()->create(['role' => 'sekolah']);

    LaporanSarpras::factory()->count(3)->create([
        'user_id' => $sekolah->id,
        'kondisi' => 'baik',
    ]);
    LaporanSarpras::factory()->create([
        'user_id' => $sekolah->id,
        'kondisi' => 'rusak_ringan',
    ]);

    $response = $this->actingAs($kabid)->get('/kabid/sarpras');

    expect($response->status())->toBe(200);
    expect($response->json('rekapitulasi'))->toBeArray();
});

it('kabid can verify sarpras report', function () {
    $kabid = User::factory()->create(['role' => 'kabid']);
    $laporan = LaporanSarpras::factory()->for(User::factory())->create([
        'status_verifikasi' => 'pending',
    ]);

    $response = $this->actingAs($kabid)->patch("/kabid/sarpras/{$laporan->id}/verify", [
        'status_verifikasi' => 'terverifikasi',
    ]);

    expect($response->status())->toBe(200);
    expect($laporan->fresh()->status_verifikasi)->toBe('terverifikasi');
});

it('activity log job is created when kabid verifies sarpras', function () {
    Queue::fake();

    $kabid = User::factory()->create(['role' => 'kabid']);
    $laporan = LaporanSarpras::factory()->for(User::factory())->create([
        'status_verifikasi' => 'pending',
    ]);

    $this->actingAs($kabid)->patch("/kabid/sarpras/{$laporan->id}/verify", [
        'status_verifikasi' => 'terverifikasi',
    ]);

    Queue::assertPushed(LogUserActivityJob::class, function ($job) use ($kabid) {
        return $job->userId === $kabid->id
            && $job->action === 'sarpras_diverifikasi';
    });
});

it('sekolah cannot access kabid routes', function () {
    $sekolah = User::factory()->create(['role' => 'sekolah']);

    $laporan = LaporanSarpras::factory()->for(User::factory()->create(['role' => 'sekolah']))->create();

    expect($this->actingAs($sekolah)->get('/kabid/pengajuan')->status())->toBe(403);
    expect($this->actingAs($sekolah)->get('/kabid/sarpras')->status())->toBe(403);
    expect($this->actingAs($sekolah)->patch("/kabid/sarpras/{$laporan->id}/verify", [
        'status_verifikasi' => 'terverifikasi',
    ])->status())->toBe(403);
});

it('admin cannot access kabid routes', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect($this->actingAs($admin)->get('/kabid/pengajuan')->status())->toBe(403);
    expect($this->actingAs($admin)->get('/kabid/sarpras')->status())->toBe(403);
});

it('guest cannot access kabid routes', function () {
    expect($this->get('/kabid/pengajuan')->status())->toBe(302);
    expect($this->get('/kabid/sarpras')->status())->toBe(302);
});
