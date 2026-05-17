<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentClassroom;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menghancurkan sisa-sisa kekacauan lama...');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

        $this->command->info('Membangun ulang struktur Siakad SMP (Shift Siang 12:30 - 17:00)...');

        // --- 1. ROLE & PERMISSIONS ---
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $roleTU = Role::firstOrCreate(['name' => 'tu', 'guard_name' => 'web']);
        $roleGuru = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $roleSiswa = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $permsGuru = [
            'view_any_assesment', 'view_assesment', 'create_assesment', 'update_assesment', 'delete_assesment',
            'view_any_presence', 'view_presence', 'create_presence', 'update_presence', 'delete_presence',
            'view_any_subject', 'view_subject',
            'view_any_classroom', 'view_classroom',
            'view_any_student', 'view_student',
            'view_any_rekapitulasi', 'view_rekapitulasi',
        ];

        foreach ($permsGuru as $p) {
            $perm = Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
            $roleGuru->givePermissionTo($perm);
        }

        $permsTU = [
            'view_any_assesment', 'view_assesment', 'create_assesment', 'update_assesment', 'delete_assesment',
            'view_any_presence', 'view_presence', 'create_presence', 'update_presence', 'delete_presence',
            'view_any_subject', 'view_subject', 'create_subject', 'update_subject', 'delete_subject',
            'view_any_classroom', 'view_classroom', 'create_classroom', 'update_classroom', 'delete_classroom',
            'view_any_student', 'view_student', 'create_student', 'update_student', 'delete_student',
            'view_any_academic_year', 'view_academic_year', 'create_academic_year', 'update_academic_year', 'delete_academic_year',
            'view_any_rekapitulasi', 'view_rekapitulasi',
        ];

        foreach ($permsTU as $p) {
            $perm = Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
            $roleTU->givePermissionTo($perm);
        }

        // --- 2. DETEKSI GENDER ---
        $valL = 'male';
        $valP = 'female';
        if (class_exists(\App\Enums\Gender::class)) {
            foreach (\App\Enums\Gender::cases() as $case) {
                $search = strtolower($case->name.$case->value);
                if (str_contains($search, 'female') || str_contains($search, 'perempuan') || $case->value === 'P') {
                    $valP = $case->value;
                } elseif (str_contains($search, 'male') || str_contains($search, 'laki') || $case->value === 'L') {
                    $valL = $case->value;
                }
            }
        }

        // --- 3. USERS (ADMIN & TU) ---
        User::create([
            'email' => 'admin@siakad.com',
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
        ])->assignRole($roleAdmin);

        User::create([
            'email' => 'tu@siakad.com',
            'name' => 'Staf Tata Usaha',
            'password' => Hash::make('password'),
        ])->assignRole($roleTU);

        // --- 4. TAHUN AJARAN (2 TAHUN / 4 SEMESTER: total 4 semester => 12 kelas nanti) ---
        $academicYearsData = [
            ['name' => '2024/2025', 'semester' => 'ganjil', 'start_date' => '2024-07-01', 'end_date' => '2024-12-31', 'is_active' => false],
            ['name' => '2024/2025', 'semester' => 'genap', 'start_date' => '2025-01-01', 'end_date' => '2025-06-30', 'is_active' => false],
            ['name' => '2025/2026', 'semester' => 'ganjil', 'start_date' => '2025-07-01', 'end_date' => '2025-12-31', 'is_active' => false],
            ['name' => '2025/2026', 'semester' => 'genap', 'start_date' => '2026-01-01', 'end_date' => '2026-06-30', 'is_active' => true],
        ];

        $semesters = [];
        foreach ($academicYearsData as $data) {
            $semesters[] = AcademicYear::create($data);
        }

        // --- 5. GURU & MAPEL (11 Pasang) ---
        $daftarMapel = [
            'Pendidikan Agama', 'PPKn', 'Bahasa Indonesia', 'Matematika',
            'Ilmu Pengetahuan Alam', 'Ilmu Pengetahuan Sosial', 'Bahasa Inggris',
            'Seni Budaya', 'Pendidikan Jasmani (PJOK)', 'Prakarya', 'Informatika',
        ];

        $namaGurus = [
            'Budi Santoso', 'Siti Aminah', 'Dwi Haryanto', 'Rina Wati',
            'Joko Susilo', 'Sri Rahayu', 'Janet Halima', 'Bambang P.',
            'Dewi Lestari', 'Agus Supriyanto', 'Nita Anggraeni',
        ];

        $gurus = [];
        $subjects = [];

        $this->command->info('Merekrut 11 Guru dan Menyiapkan 11 Mata Pelajaran...');
        foreach ($daftarMapel as $index => $namaMapel) {
            // Bikin Guru
            $guru = User::create([
                'email' => 'guru'.($index + 1).'@siakad.com',
                'name' => $namaGurus[$index].', S.Pd.',
                'password' => Hash::make('password'),
                'gender' => ($index % 2 === 0) ? $valL : $valP,
            ]);
            $guru->assignRole($roleGuru);
            $gurus[] = $guru;

            // Bikin Mapel Master (Tiap guru pegang 1 mapel)
            $subjects[] = Subject::create([
                'name' => $namaMapel,
                'teacher_id' => $guru->id,
            ]);
        }

        // --- 6. SISWA & KELAS ---
        $depanL = ['Aditya', 'Bayu', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Gilang', 'Heri', 'Indra', 'Jaka'];
        $depanP = ['Anisa', 'Bunga', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hana', 'Indah', 'Jelita'];
        $belakang = ['Saputra', 'Wijaya', 'Kusuma', 'Pratama', 'Hidayat', 'Ramadhan', 'Setiawan'];

        // Setiap semester hanya 3 kelas: 7 A, 8 A, 9 A (total 4 semester * 3 = 12 kelas)
        $daftarKelas = ['7 A', '8 A', '9 A'];
        $studentsMaster = [];

        foreach ($semesters as $sem) {
            $this->command->info("Menyusun Kelas & Siswa untuk TA {$sem->name} Semester ".ucfirst($sem->semester).'...');
            $tahunMulaiTA = (int) substr($sem->name, 0, 4);

            foreach ($daftarKelas as $indexKelas => $namaKelas) {
                // Bikin Kelas (Wali kelas dibagi rata dari daftar 11 guru)
                $kelas = Classroom::create([
                    'name' => $namaKelas,
                    'academic_year_id' => $sem->id,
                    'teacher_id' => $gurus[$indexKelas % count($gurus)]->id,
                ]);

                // Sambungin 11 Mapel ke kelas ini (Biar bisa dijadwalin)
                foreach ($subjects as $subject) {
                    $subject->classrooms()->attach($kelas->id);
                }

                // Masukin Siswa ke Kelas
                if ($sem->semester === 'ganjil') {
                    $studentsMaster[$indexKelas] = [];
                    $tingkatKelas = (int) substr($namaKelas, 0, 1);
                    $tahunMasuk = $tahunMulaiTA - ($tingkatKelas - 7);
                    $tahunLahir = $tahunMulaiTA - ($tingkatKelas + 5);

                    // Bikin 10 Siswa per Kelas
                    for ($s = 1; $s <= 10; $s++) {
                        $isLaki = (rand(1, 100) > 50);
                        $namaSiswa = ($isLaki ? $depanL[array_rand($depanL)] : $depanP[array_rand($depanP)]).' '.$belakang[array_rand($belakang)];

                        $hurufKelas = substr($namaKelas, -1);
                        $kodeAngka = ['A' => '1', 'B' => '2', 'C' => '3'][$hurufKelas] ?? '0';
                        $nisn = $tahunMasuk.$tingkatKelas.$kodeAngka.sprintf('%02d', $s);

                        $cleanName = strtolower(str_replace([' ', '.'], '', $namaSiswa));
                        $emailSiswa = 'siswa.'.$cleanName.'.'.$nisn.'@student.com';

                        $siswa = Student::create([
                            'nisn' => $nisn,
                            'name' => $namaSiswa,
                            'email' => $emailSiswa,
                            'password' => Hash::make('password'),
                            'gender' => $isLaki ? $valL : $valP,
                            'date_of_birth' => Carbon::createFromDate($tahunLahir, rand(1, 12), rand(1, 28))->format('Y-m-d'),
                        ]);

                        $siswa->assignRole($roleSiswa);
                        $studentsMaster[$indexKelas][] = $siswa;

                        StudentClassroom::create(['student_id' => $siswa->id, 'classroom_id' => $kelas->id, 'is_active' => true]);
                    }
                } else {
                    // Semester Genap: Anak Ganjil naek ke semester Genap
                    foreach ($studentsMaster[$indexKelas] as $siswa) {
                        StudentClassroom::create(['student_id' => $siswa->id, 'classroom_id' => $kelas->id, 'is_active' => true]);
                    }
                }
            }
        }

        // --- 7. JADWAL PELAJARAN (SHIFT SIANG: 12:30 - 17:00) ---
        $this->command->info('Menyusun Jadwal Pelajaran (Shift Siang: 12:30 - 17:00)...');

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // Dibagi 3 Sesi biar pas sampai jam 5 sore
        $sesiWaktu = [
            ['start' => '12:30:00', 'end' => '14:00:00'],
            ['start' => '14:00:00', 'end' => '15:30:00'],
            ['start' => '15:30:00', 'end' => '17:00:00'],
        ];

        // Kita bikin jadwal untuk SEMESTER AKTIF AJA biar kaga kelamaan *seeding*-nya
        $activeSemesterId = AcademicYear::where('is_active', true)->first()->id;
        $kelasAktif = Classroom::where('academic_year_id', $activeSemesterId)->get();

        foreach ($hariList as $hari) {
            foreach ($sesiWaktu as $waktu) {
                $subjectIndex = 0; // Reset index mapel per sesi

                foreach ($kelasAktif as $kelas) {
                    $assigned = false;
                    $attempts = 0;

                    while (! $assigned && $attempts < count($subjects)) {
                        $currentSubject = $subjects[($subjectIndex + $attempts) % count($subjects)];

                        // JURUS ANTI-BENTROK: Cek apa guru ini udah ngajar kelas lain di hari & jam yang sama
                        $isBentrok = Schedule::where('day', $hari)
                            ->where('start_time', $waktu['start'])
                            ->whereHas('classroom', fn ($q) => $q->where('academic_year_id', $activeSemesterId))
                            ->whereHas('subject', fn ($q) => $q->where('teacher_id', $currentSubject->teacher_id))
                            ->exists();

                        if (! $isBentrok) {
                            Schedule::create([
                                'classroom_id' => $kelas->id,
                                'subject_id' => $currentSubject->id,
                                'day' => $hari,
                                'start_time' => $waktu['start'],
                                'end_time' => $waktu['end'],
                            ]);
                            $assigned = true;
                            $subjectIndex++; // Ganti mapel buat kelas berikutnya
                        } else {
                            $attempts++; // Kalo bentrok, coba mapel lain
                        }
                    }
                    if (! $assigned) {
                        $subjectIndex++;
                    }
                }
            }
        }

        $this->command->info('SELESAI JON! Database Siakad siap digas! Presensi & Nilai sengaja dikosongin buat demo!');
    }
}
