<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Expense;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Expense');
    }

    public function view(AuthUser $authUser, Expense $expense): bool
    {
        return $authUser->can('View:Expense');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Expense');
    }

    public function update(AuthUser $authUser, Expense $expense): bool
    {
        return $authUser->can('Update:Expense');
    }

    public function delete(AuthUser $authUser, Expense $expense): bool
    {
        return $authUser->can('Delete:Expense');
    }

    public function restore(AuthUser $authUser, Expense $expense): bool
    {
        return $authUser->can('Restore:Expense');
    }

    public function forceDelete(AuthUser $authUser, Expense $expense): bool
    {
        return $authUser->can('ForceDelete:Expense');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Expense');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Expense');
    }

    public function replicate(AuthUser $authUser, Expense $expense): bool
    {
        return $authUser->can('Replicate:Expense');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Expense');
    }

}