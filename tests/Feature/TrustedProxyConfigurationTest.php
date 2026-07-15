<?php

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;

beforeEach(function (): void {
    TrustProxies::flushState();
});

afterEach(function (): void {
    TrustProxies::flushState();
});

it('does not trust forwarded protocol headers by default', function (): void {
    config()->set('trustedproxy.proxies');

    $request = Request::create('http://gymie.test', server: [
        'REMOTE_ADDR' => '203.0.113.10',
        'HTTP_X_FORWARDED_PROTO' => 'https',
    ]);

    app(TrustProxies::class)->handle($request, fn () => response('ok'));

    expect($request->isSecure())->toBeFalse();
});

it('trusts forwarded protocol headers from configured proxies', function (): void {
    config()->set('trustedproxy.proxies', '203.0.113.10');

    $request = Request::create('http://gymie.test', server: [
        'REMOTE_ADDR' => '203.0.113.10',
        'HTTP_X_FORWARDED_PROTO' => 'https',
    ]);

    app(TrustProxies::class)->handle($request, fn () => response('ok'));

    expect($request->isSecure())->toBeTrue();
});
