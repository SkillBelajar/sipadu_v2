<?php

use App\Jobs\LogUserActivityJob;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('activity log job is dispatched when admin creates layanan', function () {
    Queue::fake();

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/layanan', [
        'nama' => 'Pengajuan SKH',
        'syarat' => [['nama_syarat' => 'KTP']],
    ]);

    Queue::assertPushed(LogUserActivityJob::class, function ($job) use ($admin) {
        return $job->userId === $admin->id
            && $job->action === 'created_Layanan';
    });
});

it('activity log stores user_id, action, and description', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $layanan = Layanan::factory()->create(['nama' => 'Layanan B']);

    $log = \App\Models\ActivityLog::create([
        'user_id' => $admin->id,
        'action' => 'created_layanan',
        'description' => 'Admin created Layanan #'.$layanan->id,
    ]);

    expect($log->user_id)->toBe($admin->id);
    expect($log->action)->toBe('created_layanan');
    expect($log->description)->toContain('created Layanan');
});
