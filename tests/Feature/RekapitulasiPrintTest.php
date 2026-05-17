<?php

use App\Enums\AssesmentType;
use App\Enums\PresenceStatus;
use App\Models\AcademicYear;
use App\Models\Assesment;
use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Student;
use App\Models\StudentAssesment;
use App\Models\StudentClassroom;
use App\Models\StudentPresence;
use App\Models\Subject;

it('prints rekapitulasi with attendance counts and average score', function () {
    $academicYear = AcademicYear::factory()->create([
        'name' => '2025/2026',
        'semester' => 'ganjil',
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
    ]);

    $classroom = Classroom::factory()
        ->for($academicYear, 'academicYear')
        ->create(['name' => '7A']);

    $student = Student::factory()->create();

    StudentClassroom::factory()
        ->for($student, 'student')
        ->for($classroom, 'classroom')
        ->state(['is_active' => true])
        ->create();

    $subject = Subject::factory()->create();

    $assesment = Assesment::create([
        'classroom_id' => $classroom->id,
        'subject_id' => $subject->id,
        'type' => AssesmentType::MIDTERM,
    ]);

    StudentAssesment::create([
        'assesment_id' => $assesment->id,
        'student_id' => $student->id,
        'score' => 85,
    ]);

    $presence = Presence::create([
        'classroom_id' => $classroom->id,
        'date' => '2025-02-15',
    ]);

    StudentPresence::create([
        'presence_id' => $presence->id,
        'student_id' => $student->id,
        'status' => PresenceStatus::PRESENT,
    ]);

    $response = $this->get(route('cetak.rekap.final', [
        'classroom' => $classroom->id,
        'year' => $academicYear->id,
    ]));

    $response->assertOk();
    $response->assertSee('7A');
    $response->assertSee('H');
    $response->assertSee('85.00');
    $response->assertSee('1');
});
