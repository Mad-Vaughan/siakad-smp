<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assesment;
use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentClassroom;
use App\Models\Subject;
use App\Models\User;
use Carbon\CarbonPeriod;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class Ganjil2024Seeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $this->command->info('⏳ Memulai Seeder Ganjil 2024...');

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        DB::beginTransaction();

        try {
            // =========================================================================
            // 0. ROLE
            // =========================================================================
            $this->command->info('🛡️ Menyiapkan Role...');
            // 'admin' dibutuhkan oleh canAccessPanel() di User
            Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'tu',          'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'teacher',     'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'student',     'guard_name' => 'web']);

            // =========================================================================
            // 1. ADMIN & TU
            // =========================================================================
            $this->command->info('👑 Mencetak Akun Admin & TU...');

            // Gunakan role 'admin' agar lolos canAccessPanel() yang mengecek ['admin','teacher','tu']
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@admin.com'],
                [
                    'name'     => 'Super Admin',
                    'password' => 'password', // cast 'hashed' di User model akan auto-hash ini
                ]
            );
            if (! $adminUser->hasRole('admin')) {
                $adminUser->assignRole('admin');
            }

            $tuUser = User::firstOrCreate(
                ['email' => 'tu@sekolah.com'],
                [
                    'name'     => 'Staf Tata Usaha',
                    'password' => 'password', // cast 'hashed' di User model akan auto-hash ini
                ]
            );
            if (! $tuUser->hasRole('tu')) {
                $tuUser->assignRole('tu');
            }

            // =========================================================================
            // 2. TAHUN AJARAN
            // =========================================================================
            $this->command->info('📅 Membuat Tahun Ajaran Ganjil 2024...');
            $academicYear = AcademicYear::create([
                'name'       => '2024/2025',
                'semester'   => 'ganjil',
                'start_date' => '2024-07-01',
                'end_date'   => '2024-12-31',
                'is_active'  => false,
                // kolom 'seq' diisi otomatis oleh booted() di AcademicYear
            ]);

            // =========================================================================
            // 3. GURU (15 Guru)
            //    Teacher extends User, semua data masuk tabel 'users'.
            //    Tidak ada tabel terpisah untuk teacher.
            //    booted() Teacher::created akan auto assignRole('teacher').
            // =========================================================================
            $this->command->info('👨‍🏫 Mencetak 15 Guru...');
            $teachers = [];
            for ($i = 1; $i <= 15; $i++) {
                $gender = $faker->randomElement(['male', 'female']);
                // Pakai User::create() + assignRole manual agar tidak ada konflik
                // GlobalScope 'teacher' pada model Teacher akan memfilter query,
                // sehingga lebih aman membuat via User langsung lalu assign role.
                $teacher = User::create([
                    'name'              => $faker->name($gender === 'male' ? 'male' : 'female'),
                    'email'             => "guru{$i}@sekolah.com",
                    'password'          => 'password', // cast 'hashed' di User model akan auto-hash ini
                    'nip'               => $faker->unique()->numerify('198#########'),
                    'gender'            => $gender,
                    'phone'             => $faker->phoneNumber,
                    'address'           => $faker->address,
                    'employment_status' => 'Tetap',
                    'active_status'     => 'Aktif',
                ]);
                $teacher->assignRole('teacher');
                $teachers[] = $teacher;
            }

            // =========================================================================
            // 4. MATA PELAJARAN
            //    Subject::$fillable hanya ['name', 'teacher_id'] — tidak ada 'code'
            // =========================================================================
            $this->command->info('📚 Membuat Mata Pelajaran...');
            $subjectNames = [
                'Matematika', 'Bahasa Indonesia', 'IPA', 'IPS', 'Bahasa Inggris',
                'PKn', 'Seni Budaya', 'Penjasorkes', 'Prakarya', 'Agama',
            ];
            $subjects = [];
            foreach ($subjectNames as $idx => $name) {
                $subjects[] = Subject::create([
                    'name'       => $name,
                    'teacher_id' => $teachers[$idx % count($teachers)]->id,
                ]);
            }

            // =========================================================================
            // 5. KELAS (7A - 9C)
            // =========================================================================
            $this->command->info('🏫 Membangun Kelas 7A - 9C...');
            $classNames = ['7A', '7B', '7C', '8A', '8B', '8C', '9A', '9B', '9C'];
            $classrooms = [];
            foreach ($classNames as $idx => $name) {
                $classrooms[] = Classroom::create([
                    'name'             => $name,
                    'academic_year_id' => $academicYear->id,
                    'teacher_id'       => $teachers[$idx]->id,
                ]);
            }

            // =========================================================================
            // 6. SISWA (10 per kelas = 90 siswa)
            //    Student extends User, semua data masuk tabel 'users'.
            //    Kolom yang valid sesuai User::$fillable:
            //      nisn, nik, name, gender, birth_place, date_of_birth,
            //      religion, address, phone, active_status
            //    booted() Student::created akan auto assignRole('student').
            // =========================================================================
            $this->command->info('🎓 Mendaftarkan 90 Siswa (10/Kelas)...');
            $allStudents = []; // [ classroom_id => [Student, ...] ]
            foreach ($classrooms as $classroom) {
                for ($s = 1; $s <= 10; $s++) {
                    $gender = $faker->randomElement(['male', 'female']);

                    // Gunakan model Student agar booted() auto assignRole('student') jalan
                    $student = Student::create([
                        'name'          => $faker->name($gender === 'male' ? 'male' : 'female'),
                        'email'         => $faker->unique()->safeEmail,
                        'password'      => 'password', // cast 'hashed' di User model akan auto-hash ini
                        'nisn'          => $faker->unique()->numerify('00########'),
                        'nik'           => $faker->unique()->numerify('320#############'),
                        'gender'        => $gender,
                        'birth_place'   => $faker->city,
                        'date_of_birth' => $faker->dateTimeBetween('-15 years', '-12 years')->format('Y-m-d'),
                        'religion'      => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                        'address'       => $faker->address,
                        'phone'         => $faker->phoneNumber,
                        'active_status' => 'Aktif',
                    ]);

                    StudentClassroom::create([
                        'student_id'   => $student->id,
                        'classroom_id' => $classroom->id,
                        'is_active'    => true,
                    ]);

                    $allStudents[$classroom->id][] = $student;
                }
            }

            // =========================================================================
            // 7. JADWAL
            // =========================================================================
            $this->command->info('🗓️ Menyusun Jadwal Shift Siang (12.30 - 17.00)...');
            $days      = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            $timeSlots = [
                ['start' => '12:30:00', 'end' => '14:00:00'],
                ['start' => '14:00:00', 'end' => '15:30:00'],
                ['start' => '15:30:00', 'end' => '17:00:00'],
            ];

            foreach ($classrooms as $classroom) {
                foreach ($days as $day) {
                    foreach ($timeSlots as $slot) {
                        Schedule::create([
                            'classroom_id' => $classroom->id,
                            'subject_id'   => $faker->randomElement($subjects)->id,
                            'day'          => $day,
                            'start_time'   => $slot['start'],
                            'end_time'     => $slot['end'],
                        ]);
                    }
                }
            }

            // =========================================================================
            // 8. PENILAIAN (Assesment)
            //    - booted() Assesment::creating  → auto isi academic_year_id
            //    - booted() Assesment::created   → auto bikin StudentAssesment
            //      untuk semua siswa di kelas tsb.
            //    Jadi JANGAN insert StudentAssesment manual lagi!
            //    Kolom type harus sesuai enum AssesmentType — sesuaikan dengan
            //    nilai enum yang ada di project Anda. Contoh di sini memakai
            //    nilai string yang umum dipakai (assignment/quiz/midterm/final).
            // =========================================================================
            $this->command->info('📝 Input Penilaian (auto-generate StudentAssesment via model)...');
            $assessmentTypes = [
                ['type' => 'assignment', 'date' => '2024-08-15'],
                ['type' => 'quiz',       'date' => '2024-09-20'],
                ['type' => 'midterm',    'date' => '2024-10-10'],
                ['type' => 'final',      'date' => '2024-12-15'],
            ];

            // Ambil kombinasi unik classroom + subject dari jadwal
            $classroomSubjects = Schedule::select('classroom_id', 'subject_id')
                ->distinct()
                ->get();

            foreach ($classroomSubjects as $cs) {
                foreach ($assessmentTypes as $at) {
                    // booted() akan auto:
                    //   1. mengisi academic_year_id dari classroom
                    //   2. membuat StudentAssesment untuk setiap siswa di kelas
                    Assesment::create([
                        'classroom_id' => $cs->classroom_id,
                        'subject_id'   => $cs->subject_id,
                        'type'         => $at['type'],
                        'date'         => $at['date'],
                    ]);
                }
            }

            // Isi score pada StudentAssesment yang sudah dibuat oleh booted()
            // Gunakan chunk update agar tidak lambat
            $this->command->info('✏️  Mengisi nilai score pada StudentAssesment...');
            \App\Models\StudentAssesment::whereNull('score')
                ->orWhere('score', 0)
                ->chunkById(500, function ($items) use ($faker) {
                    foreach ($items as $item) {
                        $item->update(['score' => $faker->numberBetween(65, 100)]);
                    }
                });

            // =========================================================================
            // 9. PRESENSI HARIAN & MAPEL
            //    - type harus 'harian' atau 'mapel' sesuai pengecekan di booted()
            //      Presence model.
            //    - booted() Presence::created → auto bikin StudentPresence untuk
            //      semua siswa di kelas. JANGAN insert StudentPresence manual!
            //    - Karena setiap Presence::create() sudah auto-create StudentPresence,
            //      kita tidak perlu array buffer manual.
            // =========================================================================
            $this->command->info('⏱️  Mengisi Presensi Juli - Desember 2024...');

            $period = CarbonPeriod::create('2024-07-01', '2024-12-31');

            foreach ($period as $date) {
                if ($date->isWeekend()) {
                    continue;
                }

                $hari   = $this->getHariIndonesia($date->dayOfWeek);
                $dateStr = $date->format('Y-m-d');

                foreach ($classrooms as $classroom) {
                    // --- ABSENSI HARIAN ---
                    // type 'harian' → booted() akan auto-set academic_year_id dari kelas
                    // booted() created → auto-create StudentPresence untuk semua siswa
                    $dailyPresence = Presence::create([
                        'classroom_id' => $classroom->id,
                        'schedule_id'  => null,
                        'date'         => $dateStr,
                        'type'         => 'harian',
                    ]);

                    // Override status secara bulk setelah auto-create agar lebih realistis
                    $this->randomizeStudentPresenceStatus($dailyPresence);

                    // --- ABSENSI MATA PELAJARAN ---
                    // type 'mapel' → booted() akan auto-set classroom_id & academic_year_id
                    // dari jadwal. Karena classroom_id diisi oleh booted(), kita tetap kirim
                    // classroom_id di sini sebagai fallback jika schedule tidak ditemukan.
                    $schedulesToday = Schedule::where('classroom_id', $classroom->id)
                        ->where('day', $hari)
                        ->get();

                    foreach ($schedulesToday as $schedule) {
                        $subjectPresence = Presence::create([
                            'classroom_id' => $classroom->id, // fallback; booted() akan overwrite dari schedule
                            'schedule_id'  => $schedule->id,
                            'date'         => $dateStr,
                            'type'         => 'mapel',
                        ]);

                        $this->randomizeStudentPresenceStatus($subjectPresence);
                    }
                }
            }

            DB::commit();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

            $this->command->info('');
            $this->command->info('🎉 SELESAI! Database Siakad sudah terisi 1 Semester Ganjil 2024!');
            $this->command->info('--------------------------------------------------');
            $this->command->info('🔐 AKUN LOGIN:');
            $this->command->info('👨‍💻 Admin  → admin@admin.com   | password');
            $this->command->info('🗃️  TU     → tu@sekolah.com    | password');
            $this->command->info('👨‍🏫 Guru   → guru1@sekolah.com | password');
            $this->command->info('--------------------------------------------------');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            $this->command->error('❌ Error: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Update status StudentPresence yang sudah di-auto-create oleh booted() Presence
     * dengan distribusi yang realistis.
     * Lebih efisien daripada update satu per satu.
     */
    private function randomizeStudentPresenceStatus(\App\Models\Presence $presence): void
    {
        $studentPresences = $presence->studentPresences;

        $updates = $studentPresences->map(function ($sp) {
            return ['id' => $sp->id, 'status' => $this->getRandomPresenceStatus()];
        });

        // Bulk update per status agar query minimal
        foreach (['present', 'sick', 'permission', 'absent'] as $status) {
            $ids = $updates->where('status', $status)->pluck('id')->toArray();
            if (! empty($ids)) {
                \App\Models\StudentPresence::whereIn('id', $ids)->update(['status' => $status]);
            }
        }
    }

    private function getHariIndonesia(int $dayOfWeek): string
    {
        return ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$dayOfWeek];
    }

    private function getRandomPresenceStatus(): string
    {
        $rand = rand(1, 100);
        if ($rand <= 88) return 'present';
        if ($rand <= 92) return 'sick';
        if ($rand <= 96) return 'permission';
        return 'absent';
    }
}
