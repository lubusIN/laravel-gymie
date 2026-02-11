<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Subscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Subscription');
    }

    public function view(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('View:Subscription');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Subscription');
    }

    public function update(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('Update:Subscription');
    }

    public function delete(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('Delete:Subscription');
    }

    public function restore(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('Restore:Subscription');
    }

    public function forceDelete(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('ForceDelete:Subscription');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Subscription');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Subscription');
    }

    public function replicate(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('Replicate:Subscription');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Subscription');
    }

}