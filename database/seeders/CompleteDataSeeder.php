<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\StudentClassroom;
use App\Models\Subject;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CompleteDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        $this->command->info('Membangun Fondasi Sistem...');

        // ==========================================
        // 1. SETUP ROLES & ADMIN/TU
        // ==========================================
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleTeacher = Role::firstOrCreate(['name' => 'teacher']);
        $roleTU = Role::firstOrCreate(['name' => 'tu']);
        $roleStudent = Role::firstOrCreate(['name' => 'student']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@sekolah.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password'), 'gender' => 'male', 'active_status' => 'Aktif']
        );
        $superAdmin->assignRole($roleAdmin);

        // ==========================================
        // 2. GENERATE 18 GURU & 10 SISWA SPESIAL
        // ==========================================
        $this->command->info('Merekrut 18 Guru & Nyapin 10 Siswa Uji Coba...');
        $teachers = collect();
        $statusPegawai = ['PNS', 'PPPK', 'Tenaga Honor Sekolah', 'GTY/PTY'];

        for ($i = 0; $i < 18; $i++) {
            $teacher = User::create([
                'name' => $faker->name.($faker->boolean(70) ? ', S.Pd.' : ''),
                'email' => 'guru'.($i + 1).'@sekolah.com',
                'password' => Hash::make('password'),
                'gender' => $faker->randomElement(['male', 'female']),
                'nip' => $faker->unique()->numerify('198#########200#'),
                'employment_status' => $faker->randomElement($statusPegawai),
                'active_status' => 'Aktif',
                'address' => $faker->address,
                'date_of_birth' => $faker->date('Y-m-d', '1990-01-01'),
            ]);
            $teacher->assignRole($roleTeacher);
            $teachers->push($teacher);
        }

        // 👇 INI DIA 10 BOCAH SPESIAL YANG BAKAL KITA SIMULASIIN NAIK KELAS 👇
        $siswaSpesial = collect();
        for ($s = 1; $s <= 10; $s++) {
            $student = User::create([
                'name' => "Siswa Uji Coba {$s}", // Namanya gampang dicari ntar!
                'email' => "ujicoba{$s}@sekolah.com",
                'password' => Hash::make('password'),
                'nisn' => $faker->unique()->numerify('00########'),
                'gender' => $faker->randomElement(['male', 'female']),
                'address' => $faker->address,
                'date_of_birth' => $faker->date('Y-m-d', '2010-01-01'),
                'active_status' => 'Aktif',
            ]);
            $student->assignRole($roleStudent);
            $siswaSpesial->push($student);
        }

        // ==========================================
        // 3. SETUP 6 SEMESTER
        // ==========================================
        $semesters = [
            ['name' => '2023/2024', 'semester' => 'ganjil', 'start' => '2023-07-01', 'end' => '2023-12-31', 'active' => false],
            ['name' => '2023/2024', 'semester' => 'genap', 'start' => '2024-01-01', 'end' => '2024-06-30', 'active' => false],
            ['name' => '2024/2025', 'semester' => 'ganjil', 'start' => '2024-07-01', 'end' => '2024-12-31', 'active' => false],
            ['name' => '2024/2025', 'semester' => 'genap', 'start' => '2025-01-01', 'end' => '2025-06-30', 'active' => false],
            ['name' => '2025/2026', 'semester' => 'ganjil', 'start' => '2025-07-01', 'end' => '2025-12-31', 'active' => true],
            ['name' => '2025/2026', 'semester' => 'genap', 'start' => '2026-01-01', 'end' => '2026-06-30', 'active' => false],
        ];

        $kelasNames = ['7A', '7B', '7C', '8A', '8B', '8C', '9A', '9B', '9C'];
        $mapelList = ['Agama', 'PPKn', 'Bahasa Indonesia', 'Matematika', 'IPA', 'IPS', 'Bahasa Inggris', 'Seni Budaya', 'PJOK', 'Prakarya', 'Bahasa Daerah'];

        // ==========================================
        // 4. LOOPING RAKSASA (SIMULASI KENAIKAN KELAS)
        // ==========================================
        $this->command->info('Menjalankan Simulasi Kenaikan Kelas...');

        foreach ($semesters as $semIndex => $semData) {
            $academicYear = AcademicYear::create([
                'name' => $semData['name'], 'semester' => $semData['semester'],
                'start_date' => $semData['start'], 'end_date' => $semData['end'], 'is_active' => $semData['active'],
            ]);

            // Ekstrak tahun awal (2023, 2024, atau 2025) buat nentuin kelas si siswa spesial
            $tahunMulai = explode('/', $semData['name'])[0];

            foreach ($kelasNames as $cIndex => $kelasName) {
                $classroom = Classroom::create([
                    'name' => $kelasName,
                    'academic_year_id' => $academicYear->id,
                    'teacher_id' => $teachers[$cIndex]->id,
                ]);

                // 👇 RUMUS SAKTI NAIK KELAS OTOMATIS 👇
                // Kalau Tahun 2023 & Kelas 7A, ATAU Tahun 2024 & Kelas 8A, ATAU Tahun 2025 & Kelas 9A
                if (
                    ($tahunMulai === '2023' && $kelasName === '7A') ||
                    ($tahunMulai === '2024' && $kelasName === '8A') ||
                    ($tahunMulai === '2025' && $kelasName === '9A')
                ) {
                    // Masukin 10 Siswa Spesial ke kelas ini
                    foreach ($siswaSpesial as $siswa) {
                        StudentClassroom::create([
                            'student_id' => $siswa->id,
                            'classroom_id' => $classroom->id,
                            'is_active' => true,
                        ]);

                        // Kalo dia udah di kelas 9A dan semesternya Genap 2025/2026, kita LULUSIN dia!
                        if ($tahunMulai === '2025' && $semData['semester'] === 'genap' && $kelasName === '9A') {
                            $siswa->update(['active_status' => 'Alumni']);
                        }
                    }
                } else {
                    // Buat kelas-kelas lainnya, generate siswa figuran random aja
                    for ($s = 1; $s <= 10; $s++) {
                        $studentFiguran = User::create([
                            'name' => $faker->name,
                            'email' => 'figuran_'.strtolower($kelasName)."_s{$semIndex}_{$s}@sekolah.com",
                            'password' => Hash::make('password'),
                            'nisn' => $faker->unique()->numerify('00########'),
                            'gender' => $faker->randomElement(['male', 'female']),
                            'active_status' => 'Aktif',
                        ]);
                        $studentFiguran->assignRole($roleStudent);

                        StudentClassroom::create([
                            'student_id' => $studentFiguran->id,
                            'classroom_id' => $classroom->id,
                            'is_active' => true,
                        ]);
                    }
                }

                // --- Generate Mapel (Jadwal dihapus biar kaga error query lu kemaren) ---
                foreach ($mapelList as $mIndex => $mapelName) {
                    Subject::create([
                        'name' => $mapelName,
                        'teacher_id' => $teachers[$mIndex]->id,
                        'classroom_id' => $classroom->id,
                    ]);
                }
            }
        }

        $this->command->info('✅ BOOM! Simulasi Sukses! Siswa Spesial telah lulus sebagai Alumni!');
    }
}
