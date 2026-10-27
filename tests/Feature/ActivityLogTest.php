<?php

use App\Models\ActivityLog;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('activity log is created when admin creates layanan', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post('/admin/layanan', [
        'nama' => 'Pengajuan SKH',
        'syarat' => [['nama_syarat' => 'KTP']],
    ]);

    expect(ActivityLog::count())->toBeGreaterThanOrEqual(1);
    expect(ActivityLog::where('user_id', $admin->id)->exists())->toBeTrue();
    expect(ActivityLog::where('action', 'created_Layanan')->exists())->toBeTrue();
    expect(ActivityLog::where('description', 'like', '%Layanan %')->exists())->toBeTrue();
});

it('activity log stores user_id, action, and description', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $layanan = Layanan::factory()->create(['nama' => 'Layanan B']);

    $log = ActivityLog::create([
        'user_id' => $admin->id,
        'action' => 'created_layanan',
        'description' => 'Admin created Layanan #'.$layanan->id,
    ]);

    expect($log->user_id)->toBe($admin->id);
    expect($log->action)->toBe('created_layanan');
    expect($log->description)->toContain('created Layanan');
});
