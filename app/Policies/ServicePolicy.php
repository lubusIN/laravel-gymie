<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Service;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Service');
    }

    public function view(AuthUser $authUser, Service $service): bool
    {
        return $authUser->can('View:Service');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Service');
    }

    public function update(AuthUser $authUser, Service $service): bool
    {
        return $authUser->can('Update:Service');
    }

    public function delete(AuthUser $authUser, Service $service): bool
    {
        return $authUser->can('Delete:Service');
    }

    public function restore(AuthUser $authUser, Service $service): bool
    {
        return $authUser->can('Restore:Service');
    }

    public function forceDelete(AuthUser $authUser, Service $service): bool
    {
        return $authUser->can('ForceDelete:Service');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Service');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Service');
    }

    public function replicate(AuthUser $authUser, Service $service): bool
    {
        return $authUser->can('Replicate:Service');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Service');
    }

}