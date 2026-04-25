<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;
use Throwable;

class LetterheadSetting extends Settings
{
    public string $organization_name = 'SMP MUARA INDONESIA';

    public ?string $organization_tagline = 'Unggul dalam Prestasi dan Akhlak';

    public ?string $logo = null;

    public ?string $address = null;

    public ?string $phone = null;

    public ?string $email = null;

    public ?string $website = null;

    public ?string $footer_note = 'Dokumen ini dicetak otomatis oleh sistem informasi akademik.';

    public static function group(): string
    {
        return 'letterhead';
    }

    public static function defaults(): array
    {
        return [
            'organization_name' => 'SMP MUARA INDONESIA',
            'organization_tagline' => 'Unggul dalam Prestasi dan Akhlak',
            'logo' => null,
            'address' => null,
            'phone' => null,
            'email' => null,
            'website' => null,
            'footer_note' => 'Dokumen ini dicetak otomatis oleh sistem informasi akademik.',
        ];
    }

    public static function resolveWithFallback(): self
    {
        try {
            $instance = app(self::class);
            $instance->toArray();

            return $instance;
        } catch (Throwable $exception) {
            return self::fake(self::defaults(), loadMissingValues: false);
        }
    }
}
