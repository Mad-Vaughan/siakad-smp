<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPanelId = \Filament\Facades\Filament::getDefaultPanel()->getId();

        Artisan::call('shield:generate', [
            '--all' => true,
            '--option' => 'permissions',
            '--panel' => $defaultPanelId,
        ]);

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ClassroomSeeder::class,
            SubjectSeeder::class,
        ]);
    }
}
