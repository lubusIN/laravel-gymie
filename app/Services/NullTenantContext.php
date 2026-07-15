<?php

namespace App\Services;

use App\Contracts\TenantContext;

/**
 * Single-tenant implementation of TenantContext.
 *
 * Used in OSS mode where there is no active tenant.
 */
class NullTenantContext implements TenantContext
{
    public function gymId(): ?int
    {
        return null;
    }
}
