<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Invoice');
    }

    public function view(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('View:Invoice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Invoice');
    }

    public function update(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('Update:Invoice');
    }

    public function delete(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('Delete:Invoice');
    }

    public function restore(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('Restore:Invoice');
    }

    public function forceDelete(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('ForceDelete:Invoice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Invoice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Invoice');
    }

    public function replicate(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('Replicate:Invoice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Invoice');
    }

}