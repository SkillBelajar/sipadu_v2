<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin can access /admin/dashboard', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $response = $this->actingAs($user)->get('/admin/dashboard');
    expect($response->status())->toBe(200);
});

it('non-admin cannot access /admin/dashboard', function () {
    $user = User::factory()->create(['role' => 'sekolah']);
    $response = $this->actingAs($user)->get('/admin/dashboard');
    expect($response->status())->toBe(403);
});

it('guest cannot access /admin/dashboard', function () {
    $response = $this->get('/admin/dashboard');
    expect($response->status())->toBe(302);
});

it('sekolah can access /sekolah/dashboard', function () {
    $user = User::factory()->create(['role' => 'sekolah']);
    $response = $this->actingAs($user)->get('/sekolah/dashboard');
    expect($response->status())->toBe(200);
});

it('verifikator can access /verifikator/dashboard', function () {
    $user = User::factory()->create(['role' => 'verifikator']);
    $response = $this->actingAs($user)->get('/verifikator/dashboard');
    expect($response->status())->toBe(200);
});

it('kabid can access /kabid/dashboard', function () {
    $user = User::factory()->create(['role' => 'kabid']);
    $response = $this->actingAs($user)->get('/kabid/dashboard');
    expect($response->status())->toBe(200);
});

it('user can check roles with hasRole and isAdmin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    expect($admin->hasRole('admin'))->toBeTrue();
    expect($admin->isAdmin())->toBeTrue();
    expect($admin->hasRole('sekolah'))->toBeFalse();

    $sekolah = User::factory()->create(['role' => 'sekolah']);
    expect($sekolah->isSekolah())->toBeTrue();
    expect($sekolah->isAdmin())->toBeFalse();
});

it('user default role is sekolah', function () {
    $user = User::factory()->create();
    expect($user->role)->toBe('sekolah');
});
