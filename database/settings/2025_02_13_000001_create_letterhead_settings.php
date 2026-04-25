<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('letterhead.organization_name', 'SMP MUARA INDONESIA');
        $this->migrator->add('letterhead.organization_tagline', 'Unggul dalam Prestasi dan Akhlak');
        $this->migrator->add('letterhead.logo', null);
        $this->migrator->add('letterhead.address', null);
        $this->migrator->add('letterhead.phone', null);
        $this->migrator->add('letterhead.email', null);
        $this->migrator->add('letterhead.website', null);
        $this->migrator->add('letterhead.footer_note', 'Dokumen ini dicetak otomatis oleh sistem informasi akademik.');
    }

    public function down(): void
    {
        $this->migrator->delete('letterhead.organization_name');
        $this->migrator->delete('letterhead.organization_tagline');
        $this->migrator->delete('letterhead.logo');
        $this->migrator->delete('letterhead.address');
        $this->migrator->delete('letterhead.phone');
        $this->migrator->delete('letterhead.email');
        $this->migrator->delete('letterhead.website');
        $this->migrator->delete('letterhead.footer_note');
    }
};
