<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Plan;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Plan');
    }

    public function view(AuthUser $authUser, Plan $plan): bool
    {
        return $authUser->can('View:Plan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Plan');
    }

    public function update(AuthUser $authUser, Plan $plan): bool
    {
        return $authUser->can('Update:Plan');
    }

    public function delete(AuthUser $authUser, Plan $plan): bool
    {
        return $authUser->can('Delete:Plan');
    }

    public function restore(AuthUser $authUser, Plan $plan): bool
    {
        return $authUser->can('Restore:Plan');
    }

    public function forceDelete(AuthUser $authUser, Plan $plan): bool
    {
        return $authUser->can('ForceDelete:Plan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Plan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Plan');
    }

    public function replicate(AuthUser $authUser, Plan $plan): bool
    {
        return $authUser->can('Replicate:Plan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Plan');
    }

}