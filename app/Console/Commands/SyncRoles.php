<?php

namespace App\Console\Commands;

use App\Enums\Roles;
use App\Support\RolePermissions;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize roles defined in the Roles enum with the database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Synchronizing roles...');

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $defaultPanelId = Filament::getPanel('admin')->getId();

        Artisan::call('shield:generate', [
            '--all' => true,
            '--option' => 'permissions',
            '--panel' => $defaultPanelId,
        ]);

        collect(Roles::cases())->each(function (Roles $role): void {
            Role::updateOrCreate([
                'name' => $role->value,
                'guard_name' => 'web',
            ]);
        });

        $allPermissions = Permission::all();

        $adminRole = Role::findByName(Roles::ADMIN->value);

        $adminRole->syncPermissions($allPermissions);

        $teacherRole = Role::findByName(Roles::TEACHER->value);

        $teacherPermissions = Permission::query()
            ->whereIn('name', RolePermissions::teacher())
            ->get();

        $teacherRole->syncPermissions($teacherPermissions);

        $tuRole = Role::findByName(Roles::TU->value);

        $tuPermissions = Permission::query()
            ->whereIn('name', RolePermissions::tu())
            ->get();

        $tuRole->syncPermissions($tuPermissions);

        $this->info('Roles synchronized successfully.');
    }
}
