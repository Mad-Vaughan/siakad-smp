<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Programs extends Component
{
    #[Layout('components.layouts.app'), Title('SMP MUARA INDONESIA - Program Pendidikan')]
    public function render()
    {
        return view('livewire.pages.programs');
    }
}
