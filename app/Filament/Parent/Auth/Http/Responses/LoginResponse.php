<?php

namespace App\Filament\Parent\Auth\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        $panel = Filament::getPanel('parent');

        return redirect()->to($panel->getUrl() ?: '/parent');
    }
}
