<?php

namespace App\Settings;

use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Models\SettingsProperty;
use Spatie\LaravelSettings\Settings;
use Throwable;

class WebsiteSetting extends Settings
{
    public string $site_name;

    public ?string $site_logo;

    public ?string $phone;

    public ?string $address;

    public ?string $email;

    public string $hero_tagline;

    public string $hero_title;

    public string $hero_description;

    public string $hero_primary_cta_label;

    public ?string $hero_primary_cta_url;

    public ?string $hero_secondary_cta_label;

    public ?string $hero_secondary_cta_url;

    public ?string $hero_media;

    public array $hero_highlights;

    public array $highlight_stats;

    public array $excellence_features;

    public array $program_cards;

    public array $testimonials;

    public string $cta_title;

    public string $cta_description;

    public string $cta_button_label;

    public ?string $cta_button_url;

    public string $footer_message;

    public array $social_links;

    public string $theme_primary_color;

    public string $theme_secondary_color;

    public string $theme_accent_color;

    public string $theme_background_color;

    public string $theme_text_color;

    public static function group(): string
    {
        return 'website';
    }

    public static function defaults(): array
    {
        return [
            'site_name' => 'SMP MUARA INDONESIA',
            'site_logo' => null,
            'phone' => '+62 812-1234-5678',
            'address' => 'Jl. Gatot Subroto No. 1, Jakarta Selatan',
            'email' => 'info@smpmuara.sch.id',
            'hero_tagline' => 'Sekolah Ramah, Prestasi Gemilang',
            'hero_title' => 'Membuka Peluang Masa Depan Melalui Pendidikan Bermakna',
            'hero_description' => 'SMP Muara Indonesia menghadirkan pengalaman belajar yang mendorong kreativitas, karakter, dan kepemimpinan generasi muda.',
            'hero_primary_cta_label' => 'Daftar PPDB',
            'hero_primary_cta_url' => '/ppdb',
            'hero_secondary_cta_label' => 'Kunjungi Kampus',
            'hero_secondary_cta_url' => '/hubungi-kami',
            'hero_media' => null,
            'hero_highlights' => [
                'Kurikulum Merdeka terintegrasi teknologi',
                'Guru berpengalaman & bersertifikat',
                'Lingkungan belajar adaptif dan aman',
            ],
            'highlight_stats' => [
                [
                    'value' => '950+',
                    'label' => 'Siswa Aktif',
                    'description' => 'Belajar dalam komunitas yang suportif.',
                ],
                [
                    'value' => '60+',
                    'label' => 'Tenaga Pendidik',
                    'description' => 'Mentor terbaik di bidangnya.',
                ],
                [
                    'value' => '48',
                    'label' => 'Prestasi Nasional',
                    'description' => 'Bidang akademik & non-akademik.',
                ],
            ],
            'excellence_features' => [
                [
                    'title' => 'Pembelajaran Holistik',
                    'description' => 'Menggabungkan akademik, karakter, dan literasi digital dalam setiap proses belajar.',
                ],
                [
                    'title' => 'Fasilitas Modern',
                    'description' => 'Laboratorium STEAM, studio kreatif, pusat bahasa, dan perpustakaan digital.',
                ],
                [
                    'title' => 'Pembinaan Karakter',
                    'description' => 'Program mentoring dan kegiatan sosial untuk membentuk kepemimpinan siswa.',
                ],
            ],
            'program_cards' => [
                [
                    'title' => 'Akademik Unggulan',
                    'description' => 'Program akselerasi dengan pendampingan olimpiade dan literasi riset.',
                    'link_label' => 'Lihat Program',
                    'link_url' => '/program',
                ],
                [
                    'title' => 'Ekskul Prestasi',
                    'description' => 'Klub robotik, seni, bahasa, dan olahraga untuk menggali potensi siswa.',
                    'link_label' => 'Jelajahi Kegiatan',
                    'link_url' => '/kegiatan',
                ],
                [
                    'title' => 'Kemitraan Global',
                    'description' => 'Kolaborasi internasional untuk pertukaran budaya dan kompetisi global.',
                    'link_label' => 'Cari Tahu',
                    'link_url' => '/prestasi',
                ],
            ],
            'testimonials' => [
                [
                    'quote' => 'Pembelajaran di SMP Muara membuat saya percaya diri menghadapi jenjang berikutnya.',
                    'name' => 'Anisa Putri',
                    'role' => 'Alumni 2022',
                ],
                [
                    'quote' => 'Guru-gurunya mendampingi dengan tulus dan fasilitasnya sangat mendukung.',
                    'name' => 'Bapak Adi',
                    'role' => 'Orang Tua Siswa',
                ],
            ],
            'cta_title' => 'Siap Bergabung Bersama SMP Muara Indonesia?',
            'cta_description' => 'Mari wujudkan masa depan terbaik putra-putri Anda bersama lingkungan belajar yang kreatif, inovatif, dan inspiratif.',
            'cta_button_label' => 'Hubungi Tim PPDB',
            'cta_button_url' => '/hubungi-kami',
            'footer_message' => 'Mencetak generasi pembelajar yang unggul dalam prestasi dan akhlak.',
            'social_links' => [
                [
                    'label' => 'Instagram',
                    'icon' => 'instagram',
                    'url' => 'https://instagram.com/smpmuara',
                ],
                [
                    'label' => 'YouTube',
                    'icon' => 'youtube',
                    'url' => 'https://youtube.com/@smpmuara',
                ],
                [
                    'label' => 'Facebook',
                    'icon' => 'facebook',
                    'url' => 'https://facebook.com/smpmuara',
                ],
            ],
            'theme_primary_color' => '#1d6ae6',
            'theme_secondary_color' => '#f97316',
            'theme_accent_color' => '#10b981',
            'theme_background_color' => '#f6f8fb',
            'theme_text_color' => '#0f172a',
        ];
    }

    public static function resolveWithFallback(): self
    {
        try {
            $instance = app(self::class);
            $instance->toArray();

            return self::withDefaultValues($instance);
        } catch (Throwable $exception) {
            return self::fake(
                array_replace(self::defaults(), self::storedValues()),
                loadMissingValues: false
            );
        }
    }

    protected static function withDefaultValues(self $settings): self
    {
        foreach (self::defaults() as $property => $value) {
            if (! property_exists($settings, $property)) {
                continue;
            }

            if ($settings->{$property} === null) {
                $settings->{$property} = $value;
            }
        }

        return $settings;
    }

    protected static function storedValues(): array
    {
        try {
            if (! Schema::hasTable('settings')) {
                return [];
            }

            return SettingsProperty::query()
                ->where('group', self::group())
                ->pluck('payload', 'name')
                ->map(fn (string $payload) => json_decode($payload, true))
                ->toArray();
        } catch (Throwable $exception) {
            return [];
        }
    }
}
