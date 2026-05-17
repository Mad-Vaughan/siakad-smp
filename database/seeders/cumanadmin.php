<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class cumanadmin extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menghancurkan sisa-sisa kekacauan lama...');

        // Bersihin cache permission biar kaga nyangkut
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Matiin bentar gembok relasi biar bisa dihapus semua
        Schema::disableForeignKeyConstraints();
        $tables = [
            'academic_years', 'classrooms', 'student_classrooms', 'subjects', 'classroom_subject',
            'users', 'model_has_roles', 'presences', 'student_presences', 'schedules',
            'assesments', 'student_assesments', 'permissions', 'role_has_permissions',
        ];
        foreach ($tables as $t) {
            if (Schema::hasTable($t)) {
                DB::table($t)->truncate();
            }
        }
        Schema::enableForeignKeyConstraints();

        $this->command->info('Membangun Fondasi Sistem Super Bersih...');

        // --- 1. SETUP ROLES ---
        // (Role tetep harus dibikin di awal biar pas lu nambah Guru/TU lewat web kaga error)
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tu', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // --- 2. BIKIN 1 AKUN ADMIN TUNGGAL ---
        $this->command->info('Menciptakan Sang Penguasa Sistem (Super Admin)...');

        $superAdmin = User::create([
            'email' => 'admin@siakad.com',
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'gender' => 'male', // Sesuain sama field form lu
            'active_status' => 'Aktif',
        ]);

        $superAdmin->assignRole($roleAdmin);

        $this->command->info('✅ BOOM! Database suci kembali! Cuma ada 1 Akun Admin sekarang!');
    }
}
