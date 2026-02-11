<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Enquiry;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnquiryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Enquiry');
    }

    public function view(AuthUser $authUser, Enquiry $enquiry): bool
    {
        return $authUser->can('View:Enquiry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Enquiry');
    }

    public function update(AuthUser $authUser, Enquiry $enquiry): bool
    {
        return $authUser->can('Update:Enquiry');
    }

    public function delete(AuthUser $authUser, Enquiry $enquiry): bool
    {
        return $authUser->can('Delete:Enquiry');
    }

    public function restore(AuthUser $authUser, Enquiry $enquiry): bool
    {
        return $authUser->can('Restore:Enquiry');
    }

    public function forceDelete(AuthUser $authUser, Enquiry $enquiry): bool
    {
        return $authUser->can('ForceDelete:Enquiry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Enquiry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Enquiry');
    }

    public function replicate(AuthUser $authUser, Enquiry $enquiry): bool
    {
        return $authUser->can('Replicate:Enquiry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Enquiry');
    }

}