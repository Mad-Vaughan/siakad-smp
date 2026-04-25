<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Contact extends Component
{
    #[Layout('components.layouts.app'), Title('SMP MUARA INDONESIA - Hubungi Kami')]
    public function render()
    {
        return view('livewire.pages.contact');
    }
}
