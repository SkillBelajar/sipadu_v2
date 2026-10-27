<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

it('home page redirects to login', function () {
    $response = $this->get('/');
    expect($response->status())->toBe(302);
    $response->assertRedirect('/login');
});

it('can connect to MySQL database', function () {
    DB::connection()->getPdo();
    expect(DB::connection()->getDatabaseName())->toBe(':memory:');
});

it('can connect to Redis', function () {
    Cache::store('redis')->put('pest_test', 'ok', 60);
    expect(Cache::store('redis')->get('pest_test'))->toBe('ok');
    Cache::store('redis')->forget('pest_test');
});
