<?php

namespace App\Console\Commands;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignStudentRoles extends Command
{
    protected $signature = 'assign:student-roles';

    protected $description = 'Assign the student role to users who have a NISN but no student role yet.';

    public function handle(): int
    {
        $roleName = Roles::STUDENT->value;

        Role::firstOrCreate(['name' => $roleName]);

        $users = User::query()
            ->whereNotNull('nisn')
            ->where('nisn', '!=', '')
            ->get();

        $count = 0;

        foreach ($users as $user) {
            if (! $user->hasRole($roleName)) {
                $user->assignRole($roleName);
                $count++;
            }
        }

        $this->info("Assigned role '{$roleName}' to {$count} users.");

        return Command::SUCCESS;
    }
}
