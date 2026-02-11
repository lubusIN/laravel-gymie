<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Member;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Member');
    }

    public function view(AuthUser $authUser, Member $member): bool
    {
        return $authUser->can('View:Member');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Member');
    }

    public function update(AuthUser $authUser, Member $member): bool
    {
        return $authUser->can('Update:Member');
    }

    public function delete(AuthUser $authUser, Member $member): bool
    {
        return $authUser->can('Delete:Member');
    }

    public function restore(AuthUser $authUser, Member $member): bool
    {
        return $authUser->can('Restore:Member');
    }

    public function forceDelete(AuthUser $authUser, Member $member): bool
    {
        return $authUser->can('ForceDelete:Member');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Member');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Member');
    }

    public function replicate(AuthUser $authUser, Member $member): bool
    {
        return $authUser->can('Replicate:Member');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Member');
    }

}