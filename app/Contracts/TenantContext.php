<?php

namespace App\Contracts;

/**
 * Resolves the current tenant (Gym) context.
 *
 * OSS default returns null (single tenant). The platform/tenancy implementation
 * binds this to a tenant-aware resolver.
 */
interface TenantContext
{
    /**
     * @return int|null The current Gym id, or null when running single-tenant.
     */
    public function gymId(): ?int;
}
