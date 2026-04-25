<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class News extends Component
{
    #[Layout('components.layouts.app'), Title('SMP MUARA INDONESIA - Berita Sekolah')]
    public function render()
    {
        return view('livewire.pages.news');
    }
}
