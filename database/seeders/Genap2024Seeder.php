<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assesment;
use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Schedule;
use App\Models\StudentClassroom;
use Carbon\CarbonPeriod;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Genap2024Seeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $this->command->info('⏳ Memulai Mesin Waktu ke Semester Genap 2025...');

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::beginTransaction();

        try {
            // =========================================================================
            // 1. CARI TAHUN AJARAN GANJIL (SYARAT WAJIB SEBELUM LANJUT)
            // =========================================================================
            $ganjilYear = AcademicYear::where('name', '2024/2025')->where('semester', 'ganjil')->first();

            if (! $ganjilYear) {
                $this->command->error('❌ Gagal Jon! Kaga nemu data Ganjil! Pastiin lu udah jalanin Ganjil2024Seeder duluan ya!');
                return;
            }

            // =========================================================================
            // 2. BIKIN TAHUN AJARAN GENAP 2025
            // =========================================================================
            $this->command->info('📅 Membuat Tahun Ajaran Genap 2024/2025...');
            $genapYear = AcademicYear::firstOrCreate(
                ['name' => '2024/2025', 'semester' => 'genap'],
                [
                    'start_date' => '2025-01-01',
                    'end_date'   => '2025-06-30',
                    'is_active'  => true, // 👈 Genap kita set Aktif biar web lu jalan di semester ini
                ]
            );

            // =========================================================================
            // 3. COPY KELAS, SISWA, & JADWAL DARI GANJIL KE GENAP
            // =========================================================================
            $this->command->info('🏫 Menyalin Data Kelas, Siswa, dan Jadwal ke Genap...');
            $ganjilClassrooms = Classroom::where('academic_year_id', $ganjilYear->id)->get();
            $genapClassrooms = [];

            foreach ($ganjilClassrooms as $ganjilClass) {
                // Copy Kelas
                $genapClass = Classroom::firstOrCreate([
                    'name'             => $ganjilClass->name,
                    'academic_year_id' => $genapYear->id,
                    'teacher_id'       => $ganjilClass->teacher_id,
                ]);
                $genapClassrooms[] = $genapClass;

                // Copy Siswa biar tetep di kelas yang sama
                $studentIds = StudentClassroom::where('classroom_id', $ganjilClass->id)->pluck('student_id');
                foreach ($studentIds as $sId) {
                    StudentClassroom::firstOrCreate([
                        'student_id'   => $sId,
                        'classroom_id' => $genapClass->id,
                    ], ['is_active' => true]);
                }

                // Copy Jadwal Pelajaran persis sama
                $ganjilSchedules = Schedule::where('classroom_id', $ganjilClass->id)->get();
                foreach ($ganjilSchedules as $gSched) {
                    Schedule::firstOrCreate([
                        'classroom_id' => $genapClass->id,
                        'subject_id'   => $gSched->subject_id,
                        'day'          => $gSched->day,
                        'start_time'   => $gSched->start_time,
                        'end_time'     => $gSched->end_time,
                    ]);
                }
            }

            // =========================================================================
            // 4. GENERATE PENILAIAN (ASSESSMENTS) GENAP 2025
            // =========================================================================
            $this->command->info('📝 Membuat Struktur Nilai Genap (Otomatis generate StudentAssesment)...');
            $assessmentTypes = [
                ['type' => 'assignment', 'date' => '2025-02-15'],
                ['type' => 'quiz',       'date' => '2025-03-20'],
                ['type' => 'midterm',    'date' => '2025-04-10'],
                ['type' => 'final',      'date' => '2025-06-15'],
            ];

            $classroomSubjects = Schedule::whereIn('classroom_id', collect($genapClassrooms)->pluck('id'))
                ->select('classroom_id', 'subject_id')
                ->distinct()
                ->get();

            foreach ($classroomSubjects as $cs) {
                foreach ($assessmentTypes as $at) {
                    Assesment::firstOrCreate([
                        'classroom_id' => $cs->classroom_id,
                        'subject_id'   => $cs->subject_id,
                        'type'         => $at['type'],
                    ], [
                        'date' => $at['date'],
                    ]);
                }
            }

            $this->command->info('✏️  Mengisi nilai rapor Genap...');
            \App\Models\StudentAssesment::whereNull('score')->orWhere('score', 0)
                ->chunkById(500, function ($items) use ($faker) {
                    foreach ($items as $item) {
                        $item->update(['score' => $faker->numberBetween(70, 100)]); // Agak rajin di genap wkwk
                    }
                });

            // =========================================================================
            // 5. GENERATE ABSENSI HARIAN & MAPEL (JANUARI - JUNI 2025)
            // =========================================================================
            $this->command->info('⏱️  Mengisi Puluhan Ribu Presensi (1 Jan - 30 Jun 2025)...');
            
            $period = CarbonPeriod::create('2025-01-01', '2025-06-30');

            foreach ($period as $date) {
                if ($date->isWeekend()) continue;

                $hari = $this->getHariIndonesia($date->dayOfWeek);
                $dateStr = $date->format('Y-m-d');

                foreach ($genapClassrooms as $classroom) {
                    // --- ABSEN HARIAN ---
                    $dailyPresence = Presence::firstOrCreate([
                        'classroom_id' => $classroom->id,
                        'schedule_id'  => null,
                        'date'         => $dateStr,
                        'type'         => 'harian',
                    ]);
                    $this->randomizeStudentPresenceStatus($dailyPresence);

                    // --- ABSEN MAPEL ---
                    $schedulesToday = Schedule::where('classroom_id', $classroom->id)->where('day', $hari)->get();
                    foreach ($schedulesToday as $schedule) {
                        $subjectPresence = Presence::firstOrCreate([
                            'classroom_id' => $classroom->id,
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
            $this->command->info('🎉 SELESAI JON! Database Siakad lu resmi menembus 2 Semester (Ganjil & Genap)!');
            $this->command->info('Silakan login pake akun Admin atau TU lu dan nikmati data yang super padat ini! 🔥');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            $this->command->error('❌ Error: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function randomizeStudentPresenceStatus(\App\Models\Presence $presence): void
    {
        $studentPresences = $presence->studentPresences;

        $updates = $studentPresences->map(function ($sp) {
            return ['id' => $sp->id, 'status' => $this->getRandomPresenceStatus()];
        });

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
        if ($rand <= 90) return 'present'; // 90% hadir (rajin dikit pas genap)
        if ($rand <= 93) return 'sick';
        if ($rand <= 97) return 'permission';
        return 'absent';
    }
}