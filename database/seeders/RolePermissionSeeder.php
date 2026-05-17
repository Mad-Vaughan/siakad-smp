<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Bersihkan Cache Permission (Wajib biar kaga error pas ganti-ganti role)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Buat atau Ambil Role
        $tu = Role::firstOrCreate(['name' => 'tu', 'guard_name' => 'web']);
        $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        // Role 'admin' kaga usah di-setting permission-nya di sini karena udah pake Gate::before

        // 3. List Permission buat TU (Full akses operasional sekolah)
        $tuPermissions = [
            'view_any_student', 'view_student', 'create_student', 'update_student', 'delete_student',
            'view_any_teacher', 'view_teacher', 'create_teacher', 'update_teacher', 'delete_teacher',
            'view_any_classroom', 'view_classroom', 'create_classroom', 'update_classroom', 'delete_classroom',
            'view_any_subject', 'view_subject', 'create_subject', 'update_subject', 'delete_subject',
            'view_any_academic_year', 'view_academic_year', 'create_academic_year', 'update_academic_year', 'delete_academic_year',
            'view_any_presence', 'view_presence', 'create_presence', 'update_presence', 'delete_presence',
            'view_any_assesment', 'view_assesment', 'create_assesment', 'update_assesment', 'delete_assesment',
        ];

        // 4. List Permission buat Guru (Lihat data sekolah + Input Absen & Nilai)
        $teacherPermissions = [
            'view_any_student', 'view_student',
            'view_any_classroom', 'view_classroom',
            'view_any_subject', 'view_subject',
            'view_any_academic_year', 'view_academic_year',
            'view_any_presence', 'view_presence', 'create_presence', 'update_presence', 'delete_presence',
            'view_any_assesment', 'view_assesment', 'create_assesment', 'update_assesment', 'delete_assesment',
        ];

        // 5. List Permission buat Siswa (Cuma liat data & hasil belajar)
        $studentPermissions = [
            'view_any_student', 'view_student',
            'view_any_presence', 'view_presence',
            'view_any_assesment', 'view_assesment',
        ];

        // 6. Tanam semua permission ke database & tempelin ke role-nya
        $this->syncRoleWithPermissions($tu, $tuPermissions);
        $this->syncRoleWithPermissions($teacher, $teacherPermissions);
        $this->syncRoleWithPermissions($student, $studentPermissions);

        echo "ROLE & PERMISSION SEEDER COMPLETED!\n";
    }

    private function syncRoleWithPermissions($role, $permissions)
    {
        foreach ($permissions as $permName) {
            // Buat permission-nya kalo belum ada di tabel permissions
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // Tempelin semua list di atas ke role terkait
        $role->syncPermissions($permissions);
    }
}
