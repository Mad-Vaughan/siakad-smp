<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Admission extends Component
{
    #[Layout('components.layouts.app'), Title('SMP MUARA INDONESIA - Informasi Penerimaan Siswa')]
    public function render()
    {
        return view('livewire.pages.admission');
    }
}
