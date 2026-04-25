<?php

use App\Settings\WebsiteSetting;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $defaults = WebsiteSetting::defaults();

        $keys = [
            'hero_tagline',
            'hero_title',
            'hero_description',
            'hero_primary_cta_label',
            'hero_primary_cta_url',
            'hero_secondary_cta_label',
            'hero_secondary_cta_url',
            'hero_media',
            'hero_highlights',
            'highlight_stats',
            'excellence_features',
            'program_cards',
            'testimonials',
            'cta_title',
            'cta_description',
            'cta_button_label',
            'cta_button_url',
            'footer_message',
            'social_links',
            'theme_primary_color',
            'theme_secondary_color',
            'theme_accent_color',
            'theme_background_color',
            'theme_text_color',
        ];

        foreach ($keys as $key) {
            $this->migrator->add("website.{$key}", $defaults[$key] ?? null);
        }
    }

    public function down(): void
    {
        $keys = [
            'hero_tagline',
            'hero_title',
            'hero_description',
            'hero_primary_cta_label',
            'hero_primary_cta_url',
            'hero_secondary_cta_label',
            'hero_secondary_cta_url',
            'hero_media',
            'hero_highlights',
            'highlight_stats',
            'excellence_features',
            'program_cards',
            'testimonials',
            'cta_title',
            'cta_description',
            'cta_button_label',
            'cta_button_url',
            'footer_message',
            'social_links',
            'theme_primary_color',
            'theme_secondary_color',
            'theme_accent_color',
            'theme_background_color',
            'theme_text_color',
        ];

        foreach ($keys as $key) {
            $this->migrator->delete("website.{$key}");
        }
    }
};
