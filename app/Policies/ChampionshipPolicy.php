<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Championship;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChampionshipPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Championship');
    }

    public function view(AuthUser $authUser, Championship $championship): bool
    {
        return $authUser->can('View:Championship');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Championship');
    }

    public function update(AuthUser $authUser, Championship $championship): bool
    {
        return $authUser->can('Update:Championship');
    }

    public function delete(AuthUser $authUser, Championship $championship): bool
    {
        return $authUser->can('Delete:Championship');
    }

    public function restore(AuthUser $authUser, Championship $championship): bool
    {
        return $authUser->can('Restore:Championship');
    }

    public function forceDelete(AuthUser $authUser, Championship $championship): bool
    {
        return $authUser->can('ForceDelete:Championship');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Championship');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Championship');
    }

    public function replicate(AuthUser $authUser, Championship $championship): bool
    {
        return $authUser->can('Replicate:Championship');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Championship');
    }

}