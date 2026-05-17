<?php

use App\Filament\Resources\Rekapitulasis\Pages\ListRekapitulasis;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentClassroom;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('shows historical class names for rekapitulasi filtered by academic year', function () {
    $oldYear = AcademicYear::factory()->create([
        'name' => '2024/2025',
        'semester' => 'ganjil',
        'is_active' => false,
    ]);

    $currentYear = createActiveAcademicYear([
        'name' => '2025/2026',
        'semester' => 'genap',
    ]);

    $oldClassroom = Classroom::factory()
        ->for($oldYear, 'academicYear')
        ->create(['name' => 'VII A HISTORI']);

    $currentClassroom = Classroom::factory()
        ->for($currentYear, 'academicYear')
        ->create(['name' => 'VIII A AKTIF']);

    $student = Student::factory()->create(['name' => 'Siswa Histori']);

    StudentClassroom::factory()->create([
        'student_id' => $student->id,
        'classroom_id' => $oldClassroom->id,
        'is_active' => false,
    ]);

    StudentClassroom::factory()->create([
        'student_id' => $student->id,
        'classroom_id' => $currentClassroom->id,
        'is_active' => true,
    ]);

    Livewire::test(ListRekapitulasis::class)
        ->filterTable('academic_year_id', $oldYear->id)
        ->assertCanSeeTableRecords([$student])
        ->assertSee('VII A HISTORI')
        ->assertDontSee('VIII A AKTIF');
});
