<?php

use App\Enums\AssesmentType;
use App\Enums\PresenceStatus;
use App\Models\AcademicYear;
use App\Models\Assesment;
use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentAssesment;
use App\Models\StudentClassroom;
use App\Models\StudentPresence;
use App\Models\Subject;

it('creates full attendance and assessment data for a grade 7 student and then promotes them to a new academic year', function () {
    $oldYear = AcademicYear::factory()->create([
        'name' => '2024/2025',
        'semester' => 'ganjil',
        'is_active' => true,
    ]);

    $oldClassroom = Classroom::factory()->create([
        'name' => 'VII A',
        'academic_year_id' => $oldYear->id,
    ]);

    $student = Student::factory()->create([
        'name' => 'Siswa Kelas 7',
    ]);

    StudentClassroom::factory()->create([
        'student_id' => $student->id,
        'classroom_id' => $oldClassroom->id,
        'is_active' => true,
    ]);

    $subjects = collect([
        Subject::factory()->create(['name' => 'Matematika']),
        Subject::factory()->create(['name' => 'IPA']),
        Subject::factory()->create(['name' => 'IPS']),
    ]);

    $schedules = $subjects->map(function (Subject $subject) use ($oldClassroom) {
        return Schedule::create([
            'classroom_id' => $oldClassroom->id,
            'subject_id' => $subject->id,
            'day' => 'Senin',
            'start_time' => '07:00',
            'end_time' => '08:00',
        ]);
    });

    $dailyPresence = Presence::create([
        'classroom_id' => $oldClassroom->id,
        'type' => 'harian',
        'date' => now()->toDateString(),
    ]);

    StudentPresence::where('presence_id', $dailyPresence->id)
        ->update(['status' => PresenceStatus::PRESENT]);

    $schedules->each(function (Schedule $schedule) {
        $mapelPresence = Presence::create([
            'schedule_id' => $schedule->id,
            'type' => 'mapel',
            'date' => now()->toDateString(),
        ]);

        StudentPresence::where('presence_id', $mapelPresence->id)
            ->update(['status' => PresenceStatus::PRESENT]);
    });

    $subjects->each(function (Subject $subject) use ($oldClassroom) {
        $assesment = Assesment::create([
            'classroom_id' => $oldClassroom->id,
            'subject_id' => $subject->id,
            'type' => AssesmentType::EXAM,
        ]);

        StudentAssesment::where('assesment_id', $assesment->id)
            ->update(['score' => 90]);
    });

    expect(StudentPresence::where('student_id', $student->id)->count())->toBe(4);
    expect(StudentPresence::where('student_id', $student->id)->where('status', PresenceStatus::PRESENT)->count())->toBe(4);
    expect(StudentAssesment::where('student_id', $student->id)->count())->toBe(3);
    expect((float) StudentAssesment::where('student_id', $student->id)->avg('score'))->toBe(90.0);

    $newYear = AcademicYear::factory()->create([
        'name' => '2025/2026',
        'semester' => 'genap',
        'is_active' => true,
    ]);

    $newClassroom = Classroom::factory()->create([
        'name' => 'VIII A',
        'academic_year_id' => $newYear->id,
    ]);

    StudentClassroom::factory()->create([
        'student_id' => $student->id,
        'classroom_id' => $newClassroom->id,
        'is_active' => true,
    ]);

    expect(StudentClassroom::where('student_id', $student->id)->where('is_active', true)->count())->toBe(1);
    expect($student->refresh()->classroom->name)->toBe('VIII A');
    expect(StudentClassroom::where('classroom_id', $oldClassroom->id)->first()->is_active)->toBeFalse();
});
