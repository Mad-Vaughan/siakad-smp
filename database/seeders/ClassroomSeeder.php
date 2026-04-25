<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentClassroom;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::factory()->create([
            'name' => '2025/2026',
            'is_active' => true,
            'start_date' => now()->startOfYear(),
            'end_date' => now()->addYear(),
        ]);

        $classrooms = Classroom::factory(5)->create([
            'academic_year_id' => $academicYear->id,
        ]);

        foreach ($classrooms as $classroom) {
            $students = Student::factory(30)->create();

            foreach ($students as $student) {
                StudentClassroom::create([
                    'student_id' => $student->id,
                    'classroom_id' => $classroom->id,
                    'is_active' => true,
                ]);
            }
        }
    }
}
