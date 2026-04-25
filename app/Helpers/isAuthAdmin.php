<?php

use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;

if (! function_exists('isAuthAdmin')) {

    /**
     * current auth user is admin
     */
    function isAuthAdmin(): ?bool
    {
        return Auth::user()?->hasRole(Roles::ADMIN);
    }
}
