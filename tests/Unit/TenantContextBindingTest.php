<?php

use App\Contracts\TenantContext;
use App\Providers\AppServiceProvider;
use App\Services\NullTenantContext;
use Illuminate\Foundation\Application;

it('registers a singleton tenant context for single-tenant installations', function (): void {
    $application = new Application;

    (new AppServiceProvider($application))->register();

    $tenantContext = $application->make(TenantContext::class);

    expect($tenantContext)
        ->toBeInstanceOf(NullTenantContext::class)
        ->and($tenantContext->gymId())->toBeNull()
        ->and($application->make(TenantContext::class))->toBe($tenantContext);
});

it('preserves a tenant context registered by an add-on', function (): void {
    $application = new Application;
    $tenantContext = new class implements TenantContext
    {
        public function gymId(): ?int
        {
            return 42;
        }
    };

    $application->singleton(TenantContext::class, fn (): TenantContext => $tenantContext);

    (new AppServiceProvider($application))->register();

    expect($application->make(TenantContext::class))
        ->toBe($tenantContext)
        ->and($application->make(TenantContext::class)->gymId())->toBe(42);
});
