<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class TuPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Tu');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Tu');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Tu');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Tu');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Tu');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Tu');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Tu');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Tu');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Tu');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Tu');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Tu');
    }

}