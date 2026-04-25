<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Presence;
use Illuminate\Auth\Access\HandlesAuthorization;

class PresencePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Presence');
    }

    public function view(AuthUser $authUser, Presence $presence): bool
    {
        return $authUser->can('View:Presence');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Presence');
    }

    public function update(AuthUser $authUser, Presence $presence): bool
    {
        return $authUser->can('Update:Presence');
    }

    public function delete(AuthUser $authUser, Presence $presence): bool
    {
        return $authUser->can('Delete:Presence');
    }

    public function restore(AuthUser $authUser, Presence $presence): bool
    {
        return $authUser->can('Restore:Presence');
    }

    public function forceDelete(AuthUser $authUser, Presence $presence): bool
    {
        return $authUser->can('ForceDelete:Presence');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Presence');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Presence');
    }

    public function replicate(AuthUser $authUser, Presence $presence): bool
    {
        return $authUser->can('Replicate:Presence');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Presence');
    }

}