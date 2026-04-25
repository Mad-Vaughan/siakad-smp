<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Teacher');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Teacher');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Teacher');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Teacher');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Teacher');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Teacher');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Teacher');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Teacher');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Teacher');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Teacher');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Teacher');
    }

}