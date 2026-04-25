<?php

use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;

if (! function_exists('isAuthTeacher')) {

    /**
     * current auth user is teacher
     */
    function isAuthTeacher(): ?bool
    {
        return Auth::user()?->hasRole(Roles::TEACHER);
    }
}
