<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;

class AdminLogin extends BaseLogin
{
    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            $this->getParentLoginAction(),
            $this->getBackToHomeAction(),
        ];
    }

    protected function getParentLoginAction(): Action
    {
        return Action::make('parentLogin')
            ->label('Masuk sebagai Orang Tua atau Siswa')
            ->url(Filament::getPanel('parent')?->getLoginUrl() ?? url('/parent/login'))
            ->color('gray')
            ->outlined();
    }

    protected function getBackToHomeAction(): Action
    {
        return Action::make('backToHome')
            ->label('Kembali ke Halaman Utama')
            ->url(url('/'))
            ->color('gray')
            ->outlined();
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk sebagai Admin / Guru');
    }
}
