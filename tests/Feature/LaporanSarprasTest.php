<?php

use App\Jobs\LogUserActivityJob;
use App\Models\LaporanSarpras;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('sekolah can submit sarpras report with image', function () {
    Storage::fake('public');

    $sekolah = User::factory()->create(['role' => 'sekolah']);

    $payload = [
        'nama_ruangan' => 'Ruangan Kelas 1',
        'kondisi' => 'rusak_ringan',
        'foto' => UploadedFile::fake()->create('ruangan.jpg', 1024, 'image/jpeg'),
    ];

    $response = $this->actingAs($sekolah)->post('/sekolah/sarpras', $payload);

    expect($response->status())->toBe(201);
    expect(LaporanSarpras::count())->toBe(1);
    expect(LaporanSarpras::first()->status_verifikasi)->toBe('pending');
    Storage::disk('public')->assertExists(LaporanSarpras::first()->foto_path);
});

it('invalid validation returns error', function () {
    $sekolah = User::factory()->create(['role' => 'sekolah']);

    $payload = [
        'nama_ruangan' => '',
        'kondisi' => 'tidak_valid',
        'foto' => UploadedFile::fake()->create('dokumen.pdf', 1024, 'application/pdf'),
    ];

    $response = $this->actingAs($sekolah)->post('/sekolah/sarpras', $payload);

    expect($response->status())->toBe(302);
    $response->assertSessionHasErrors(['nama_ruangan', 'kondisi', 'foto']);
});

it('sekolah can access sarpras index and sees only own reports', function () {
    $sekolah = User::factory()->create(['role' => 'sekolah']);
    $other = User::factory()->create(['role' => 'sekolah']);

    LaporanSarpras::factory()->count(2)->create(['user_id' => $sekolah->id]);
    LaporanSarpras::factory()->create(['user_id' => $other->id]);

    $response = $this->actingAs($sekolah)->get('/sekolah/sarpras');

    expect($response->status())->toBe(200);
    expect($response->json('data'))->toHaveCount(2);
    foreach ($response->json('data') as $item) {
        expect($item['user_id'])->toBe($sekolah->id);
    }
});

it('activity log job is dispatched when sekolah reports sarpras', function () {
    Queue::fake();
    Storage::fake('public');

    $sekolah = User::factory()->create(['role' => 'sekolah']);

    $this->actingAs($sekolah)->post('/sekolah/sarpras', [
        'nama_ruangan' => 'Ruangan Kelas 2',
        'kondisi' => 'baik',
        'foto' => UploadedFile::fake()->create('ruangan.jpg', 1024, 'image/jpeg'),
    ]);

    Queue::assertPushed(LogUserActivityJob::class, function ($job) use ($sekolah) {
        return $job->userId === $sekolah->id
            && $job->action === 'laporan_sarpras_dibuat';
    });
});

it('admin cannot submit sarpras', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $payload = [
        'nama_ruangan' => 'Ruangan X',
        'kondisi' => 'baik',
        'foto' => UploadedFile::fake()->create('ruangan.jpg', 1024, 'image/jpeg'),
    ];

    $response = $this->actingAs($admin)->post('/sekolah/sarpras', $payload);

    expect($response->status())->toBe(403);
});

it('guest cannot submit sarpras', function () {
    $payload = [
        'nama_ruangan' => 'Ruangan X',
        'kondisi' => 'baik',
        'foto' => UploadedFile::fake()->create('ruangan.jpg', 1024, 'image/jpeg'),
    ];

    $response = $this->post('/sekolah/sarpras', $payload);

    expect($response->status())->toBe(302);
});
