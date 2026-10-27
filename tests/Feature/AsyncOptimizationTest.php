<?php

use App\Jobs\LogUserActivityJob;
use App\Models\Layanan;
use App\Models\LaporanSarpras;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Models\User;

uses(RefreshDatabase::class);

it('admin creating layanan dispatches LogUserActivityJob', function () {
    Queue::fake();

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/layanan', [
        'nama' => 'Layanan Tes',
        'deskripsi' => 'desc',
        'syarat' => [
            ['nama_syarat' => 'KTP', 'tipe_file' => 'pdf'],
        ],
    ]);

    Queue::assertPushed(LogUserActivityJob::class, function ($job) use ($admin) {
        return $job->userId === $admin->id
            && $job->action === 'created_Layanan';
    });
});

it('kabid verifying sarpras dispatches LogUserActivityJob', function () {
    Queue::fake();

    $kabid = User::factory()->create(['role' => 'kabid']);
    $laporan = LaporanSarpras::factory()->for(User::factory()->create(['role' => 'sekolah']))->create();

    $this->actingAs($kabid)->patch("/kabid/sarpras/{$laporan->id}/verify", [
        'status_verifikasi' => 'terverifikasi',
    ]);

    Queue::assertPushed(LogUserActivityJob::class, function ($job) use ($kabid) {
        return $job->userId === $kabid->id
            && $job->action === 'sarpras_diverifikasi';
    });
});

it('queue connection is redis', function () {
    expect(config('queue.default'))->toBe('redis');
});
