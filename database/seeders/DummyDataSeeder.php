<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\StudentClassroom;
use App\Models\Presence;
use App\Models\StudentPresence;
use App\Models\Subject;
use App\Models\Assesment;
use App\Models\StudentAssesment;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // 1. Setup Role & Admin Utama
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleTeacher = Role::firstOrCreate(['name' => 'teacher']);
        $roleTU = Role::firstOrCreate(['name' => 'tu']);
        $roleStudent = Role::firstOrCreate(['name' => 'student']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($roleAdmin);

        // 2. Setup 3 Mata Pelajaran buat Ngetes Nilai
        $subjects = ['Matematika', 'Bahasa Indonesia', 'Ilmu Pengetahuan Alam'];
        $createdSubjects = collect();
        foreach ($subjects as $sub) {
            if (class_exists(Subject::class)) {
                $createdSubjects->push(Subject::firstOrCreate(['name' => $sub]));
            }
        }

        // 3. Buat Guru (15 orang) - Data Lengkap
        $teachers = collect();
        for ($i = 1; $i <= 15; $i++) {
            $guru = User::create([
                'name' => "Guru " . $faker->firstName,
                'email' => "guru{$i}_" . uniqid() . "@sekolah.com",
                'password' => Hash::make('password'),
                'address' => $faker->address,
                'date_of_birth' => $faker->date('Y-m-d', '1990-01-01'), 
                'gender' => $faker->randomElement(['male', 'female']),
            ]);
            $guru->assignRole($roleTeacher);
            $teachers->push($guru);
        }

        // 4. Buat TU (5 orang) - Data Lengkap
        for ($i = 1; $i <= 5; $i++) {
            $tu = User::create([
                'name' => "TU " . $faker->firstName,
                'email' => "tu{$i}_" . uniqid() . "@sekolah.com",
                'password' => Hash::make('password'),
                'address' => $faker->address,
                'date_of_birth' => $faker->date('Y-m-d', '1995-01-01'),
                'gender' => $faker->randomElement(['male', 'female']),
            ]);
            $tu->assignRole($roleTU);
        }

        // 5. Buat CUMA 1 Tahun Ajaran
        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-04-01', 
            'end_date' => '2027-03-31',
            'is_active' => true, // Langsung Aktif
        ]);

        $tingkat = ['7', '8', '9'];
        $abjad = ['A', 'B', 'C'];

        foreach ($tingkat as $t) {
            foreach ($abjad as $a) {
                // Buat Kelas
                $classroom = Classroom::create([
                    'name' => "{$t} {$a}",
                    'academic_year_id' => $academicYear->id,
                    'teacher_id' => $teachers->random()->id,
                ]);

                // Buat 30 Siswa Data Lengkap
                for ($s = 1; $s <= 30; $s++) {
                    $student = User::create([
                        'name' => "Siswa {$t}{$a} - " . $faker->firstName,
                        'email' => "siswa_" . uniqid() . "@sekolah.com",
                        'password' => Hash::make('password'),
                        'nisn' => $faker->unique()->numerify('##########'),
                        'address' => $faker->address,
                        'date_of_birth' => $faker->date('Y-m-d', '2010-12-31'),
                        'gender' => $faker->randomElement(['male', 'female']),
                    ]);
                    $student->assignRole($roleStudent);

                    // Masukin ke kelas
                    StudentClassroom::create([
                        'student_id' => $student->id,
                        'classroom_id' => $classroom->id,
                        'is_active' => true,
                    ]);
                }

                // 👇 GENERATOR ABSEN & NILAI ACAK 👇
                try {
                    // A. Bikin 5 Hari Presensi (Mundur dari hari ini)
                    for ($hari = 1; $hari <= 5; $hari++) {
                        $date = date('Y-m-d', strtotime("-$hari days"));
                        $presence = Presence::create([
                            'classroom_id' => $classroom->id,
                            'date' => $date,
                        ]);

                        // Acak status absen siswanya
                        foreach ($presence->studentPresences as $sp) {
                            $rand = rand(1, 100);
                            if ($rand <= 80) $status = \App\Enums\PresenceStatus::PRESENT; // 80% Peluang Hadir
                            elseif ($rand <= 85) $status = \App\Enums\PresenceStatus::SICK;
                            elseif ($rand <= 95) $status = \App\Enums\PresenceStatus::PERMISSION;
                            else $status = \App\Enums\PresenceStatus::ABSENT;
                            
                            $sp->update(['status' => $status]);
                        }
                    }

                    // B. Bikin Penilaian (Nilai Ujian Acak)
                    if (class_exists(Assesment::class) && $createdSubjects->isNotEmpty()) {
                        foreach ($createdSubjects as $subject) {
                            $assesment = Assesment::create([
                                'classroom_id' => $classroom->id,
                                'subject_id' => $subject->id,
                                'type' => \App\Enums\AssesmentType::cases()[0] ?? null,
                            ]);

                            // Acak nilai ujian dari 65 sampe 100
                            foreach ($assesment->studentAssesments as $sa) {
                                // PENTING: Sesuaiin sama nama kolom nilai lo (biasanya 'score' atau 'nilai')
                                $sa->update(['score' => rand(65, 100)]); 
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Gagal generate data absen/nilai: " . $e->getMessage());
                }
            }
        }

        echo "MANTAP JON! Data 1 Tahun Ajaran Lengkap + Absen & Nilai berhasil dibuat!\n";
    }
}