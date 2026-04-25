<?php

use App\Filament\Pages\ManageWebsite;
use App\Settings\WebsiteSetting;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can update website settings via the CMS form', function () {
    $payload = [
        'hero_title' => 'Sekolah Masa Depan',
        'hero_highlights' => [
            'Pembelajaran kreatif',
            'Fasilitas modern',
        ],
        'highlight_stats' => [
            [
                'value' => '1000+',
                'label' => 'Siswa',
                'description' => 'Aktif di berbagai kegiatan.',
            ],
            [
                'value' => '75+',
                'label' => 'Tenaga Pendidik',
                'description' => 'Profesional dan tersertifikasi.',
            ],
        ],
        'theme_primary_color' => '#112233',
        'theme_secondary_color' => '#445566',
        'cta_button_label' => 'Hubungi Kami',
        'social_links' => [
            [
                'label' => 'Instagram',
                'icon' => 'instagram',
                'url' => 'https://instagram.com/smpmuara',
            ],
        ],
    ];

    Livewire::test(ManageWebsite::class)
        ->set('data.hero_title', $payload['hero_title'])
        ->set('data.hero_highlights', $payload['hero_highlights'])
        ->set('data.highlight_stats', $payload['highlight_stats'])
        ->set('data.theme_primary_color', $payload['theme_primary_color'])
        ->set('data.theme_secondary_color', $payload['theme_secondary_color'])
        ->set('data.cta_button_label', $payload['cta_button_label'])
        ->set('data.social_links', $payload['social_links'])
        ->call('save')
        ->assertHasNoErrors();

    app()->forgetInstance(WebsiteSetting::class);
    $settings = app(WebsiteSetting::class);

    expect($settings->hero_title)->toEqual($payload['hero_title']);
    expect($settings->hero_highlights)->toMatchArray($payload['hero_highlights']);
    expect($settings->highlight_stats)->toMatchArray($payload['highlight_stats']);
    expect($settings->theme_primary_color)->toEqual($payload['theme_primary_color']);
    expect($settings->theme_secondary_color)->toEqual($payload['theme_secondary_color']);
    expect($settings->cta_button_label)->toEqual($payload['cta_button_label']);
    expect($settings->social_links)->toMatchArray($payload['social_links']);
});

it('renders customized values on the landing page', function () {
    $settings = WebsiteSetting::resolveWithFallback();
    $settings->hero_title = 'Sekolah Kolaboratif';
    $settings->theme_primary_color = '#221144';
    $settings->theme_secondary_color = '#ff9900';
    $settings->footer_message = 'Footer test message';
    $settings->save();

    app()->forgetInstance(WebsiteSetting::class);

    $this->get(route('home'))
        ->assertSee('Sekolah Kolaboratif', escape: false)
        ->assertSee('#221144', escape: false)
        ->assertSee('Footer test message', escape: false);
});
