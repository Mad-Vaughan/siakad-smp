<?php

namespace App\Livewire\Pages;

use App\Settings\WebsiteSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Home extends Component
{
    #[Layout('components.layouts.app'), Title('SMP MUARA INDONESIA - Home')]
    public function render()
    {
        return view('livewire.pages.home', [
            'settings' => WebsiteSetting::resolveWithFallback(),
        ]);
    }
}
