<?php

namespace App\Support;

class SiteNavigation
{
    public static function primary(): array
    {
        return [
            ['label' => 'Beranda', 'route' => 'home'],
            ['label' => 'Profil', 'route' => 'about'],
            ['label' => 'Program', 'route' => 'programs'],
            ['label' => 'Prestasi', 'route' => 'achievements'],
            ['label' => 'Kegiatan', 'route' => 'activities'],
            ['label' => 'Berita', 'route' => 'news'],
            ['label' => 'Hubungi', 'route' => 'contact'],
        ];
    }
}
