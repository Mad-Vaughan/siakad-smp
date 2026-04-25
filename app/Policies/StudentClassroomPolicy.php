<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StudentClassroom;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentClassroomPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudentClassroom');
    }

    public function view(AuthUser $authUser, StudentClassroom $studentClassroom): bool
    {
        return $authUser->can('View:StudentClassroom');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudentClassroom');
    }

    public function update(AuthUser $authUser, StudentClassroom $studentClassroom): bool
    {
        return $authUser->can('Update:StudentClassroom');
    }

    public function delete(AuthUser $authUser, StudentClassroom $studentClassroom): bool
    {
        return $authUser->can('Delete:StudentClassroom');
    }

    public function restore(AuthUser $authUser, StudentClassroom $studentClassroom): bool
    {
        return $authUser->can('Restore:StudentClassroom');
    }

    public function forceDelete(AuthUser $authUser, StudentClassroom $studentClassroom): bool
    {
        return $authUser->can('ForceDelete:StudentClassroom');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudentClassroom');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudentClassroom');
    }

    public function replicate(AuthUser $authUser, StudentClassroom $studentClassroom): bool
    {
        return $authUser->can('Replicate:StudentClassroom');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudentClassroom');
    }

}