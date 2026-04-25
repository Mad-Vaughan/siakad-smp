<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Assesment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssesmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Assesment');
    }

    public function view(AuthUser $authUser, Assesment $assesment): bool
    {
        return $authUser->can('View:Assesment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Assesment');
    }

    public function update(AuthUser $authUser, Assesment $assesment): bool
    {
        return $authUser->can('Update:Assesment');
    }

    public function delete(AuthUser $authUser, Assesment $assesment): bool
    {
        return $authUser->can('Delete:Assesment');
    }

    public function restore(AuthUser $authUser, Assesment $assesment): bool
    {
        return $authUser->can('Restore:Assesment');
    }

    public function forceDelete(AuthUser $authUser, Assesment $assesment): bool
    {
        return $authUser->can('ForceDelete:Assesment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Assesment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Assesment');
    }

    public function replicate(AuthUser $authUser, Assesment $assesment): bool
    {
        return $authUser->can('Replicate:Assesment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Assesment');
    }

}