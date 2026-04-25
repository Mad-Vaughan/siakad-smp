<?php

namespace App\Livewire;

use App\Settings\WebsiteSetting;
use App\Support\SiteNavigation;
use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        $settings = WebsiteSetting::resolveWithFallback();
        $navLinks = SiteNavigation::primary();

        return view('livewire.footer', [
            'settings' => $settings,
            'navLinks' => $navLinks,
            'socialLinks' => collect($settings->social_links ?? [])
                ->filter(fn (array $link) => filled($link['label'] ?? null) && filled($link['url'] ?? null))
                ->values(),
        ]);
    }
}
