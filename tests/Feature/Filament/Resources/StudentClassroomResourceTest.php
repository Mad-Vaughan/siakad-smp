<?php

use App\Filament\Resources\StudentClassrooms\Pages\CreateStudentClassroom;
use App\Filament\Resources\StudentClassrooms\Pages\EditStudentClassroom;
use App\Filament\Resources\StudentClassrooms\Pages\ListStudentClassrooms;
use App\Filament\Resources\StudentClassrooms\Pages\ViewStudentClassroom;
use App\Filament\Resources\Students\StudentResource;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentClassroom;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render student classroom list page', function () {
    $this->get(ListStudentClassrooms::getUrl())
        ->assertOk();
});

it('shows student classroom records in the table', function () {
    $studentClassroom = StudentClassroom::factory()->create();

    Livewire::test(ListStudentClassrooms::class)
        ->assertCanSeeTableRecords([$studentClassroom])
        ->assertActionExists('create');
});

it('can create a student classroom and deactivate previous active assignment', function () {
    $student = Student::factory()->create();
    $firstClassroom = Classroom::factory()
        ->for(createActiveAcademicYear(), 'academicYear')
        ->create();

    StudentClassroom::factory()
        ->for($student, 'student')
        ->for($firstClassroom, 'classroom')
        ->create(['is_active' => true]);

    $secondClassroom = Classroom::factory()
        ->for(createActiveAcademicYear(), 'academicYear')
        ->create();

    $payload = [
        'student_id' => $student->getKey(),
        'classroom_id' => $secondClassroom->getKey(),
        'is_active' => true,
    ];

    Livewire::test(CreateStudentClassroom::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('student_classrooms', [
        'student_id' => $student->getKey(),
        'classroom_id' => $secondClassroom->getKey(),
        'is_active' => true,
    ]);

    $firstAssignment = StudentClassroom::where('student_id', $student->getKey())
        ->where('classroom_id', $firstClassroom->getKey())
        ->first();

    expect((bool) $firstAssignment->is_active)->toBeFalse();
});

it('can update a student classroom record', function () {
    $studentClassroom = StudentClassroom::factory()->create(['is_active' => false]);

    Livewire::test(EditStudentClassroom::class, ['record' => $studentClassroom->getKey()])
        ->fillForm([
            'student_id' => $studentClassroom->student_id,
            'classroom_id' => $studentClassroom->classroom_id,
            'is_active' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((bool) $studentClassroom->refresh()->is_active)->toBeTrue();
});

it('can view student classroom details', function () {
    $studentClassroom = StudentClassroom::factory()->create();

    Livewire::test(ViewStudentClassroom::class, ['record' => $studentClassroom->getKey()])
        ->assertSet('record.classroom_id', $studentClassroom->classroom_id)
        ->assertActionExists('edit');
});

it('redirects to student detail from custom view action', function () {
    $studentClassroom = StudentClassroom::factory()->create();

    Livewire::test(ListStudentClassrooms::class)
        ->callTableAction('view', $studentClassroom->getKey())
        ->assertRedirect(StudentResource::getUrl('view', ['record' => $studentClassroom->student_id]));
});

it('can delete student classroom entries via bulk action', function () {
    $studentClassrooms = StudentClassroom::factory()->count(2)->create();

    Livewire::test(ListStudentClassrooms::class)
        ->callTableBulkAction('delete', $studentClassrooms->pluck('id')->all());

    expect(StudentClassroom::whereKey($studentClassrooms->pluck('id'))->exists())->toBeFalse();
});
