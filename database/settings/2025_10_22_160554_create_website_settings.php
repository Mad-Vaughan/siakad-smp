<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('website.site_name', 'SMP MUARA INDONESIA');
        $this->migrator->add('website.site_logo', null);
        $this->migrator->add('website.phone', null);
        $this->migrator->add('website.address', null);
        $this->migrator->add('website.email', null);
    }

    public function down(): void
    {
        $this->migrator->delete('website.site_name');
        $this->migrator->delete('website.site_logo');
        $this->migrator->delete('website.phone');
        $this->migrator->delete('website.address');
        $this->migrator->delete('website.email');
    }
};
