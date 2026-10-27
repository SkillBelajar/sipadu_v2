<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

it('home page returns HTTP 200', function () {
    $response = $this->get('/');
    expect($response->status())->toBe(200);
});

it('can connect to MySQL database', function () {
    DB::connection()->getPdo();
    expect(DB::connection()->getDatabaseName())->toBe('sipadu');
});

it('can connect to Redis', function () {
    Cache::store('redis')->put('pest_test', 'ok', 60);
    expect(Cache::store('redis')->get('pest_test'))->toBe('ok');
    Cache::store('redis')->forget('pest_test');
});
