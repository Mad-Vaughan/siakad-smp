<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Activities extends Component
{
    #[Layout('components.layouts.app'), Title('SMP MUARA INDONESIA - Agenda & Kegiatan')]
    public function render()
    {
        return view('livewire.pages.activities');
    }
}
