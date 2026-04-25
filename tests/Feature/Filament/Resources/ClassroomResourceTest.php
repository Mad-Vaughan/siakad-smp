<?php

use App\Enums\AssesmentType;
use App\Filament\Resources\Classrooms\Pages\CreateClassroom;
use App\Filament\Resources\Classrooms\Pages\EditClassroom;
use App\Filament\Resources\Classrooms\Pages\ListClassrooms;
use App\Filament\Resources\Classrooms\Pages\ViewClassroom;
use App\Filament\Resources\Classrooms\RelationManagers\AssesmentsRelationManager;
use App\Filament\Resources\Classrooms\RelationManagers\PresencesRelationManager;
use App\Filament\Resources\Classrooms\RelationManagers\StudentsRelationManager;
use App\Models\Assesment;
use App\Models\Classroom;
use App\Models\Presence;
use App\Models\Subject;
use App\Models\Teacher;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render classroom list page', function () {
    $this->get(ListClassrooms::getUrl())
        ->assertOk();
});

it('shows classrooms in the table', function () {
    $classroom = Classroom::factory()
        ->for(createActiveAcademicYear(), 'academicYear')
        ->create(['name' => 'VII A']);

    Livewire::test(ListClassrooms::class)
        ->assertCanSeeTableRecords([$classroom])
        ->assertActionExists('create');
});

it('can create a classroom', function () {
    $teacher = Teacher::factory()->create();
    $academicYear = createActiveAcademicYear();

    $payload = [
        'academic_year_id' => $academicYear->getKey(),
        'teacher_id' => $teacher->getKey(),
        'name' => 'VIII B',
    ];

    Livewire::test(CreateClassroom::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('classrooms', [
        'name' => 'VIII B',
        'academic_year_id' => $academicYear->getKey(),
        'teacher_id' => $teacher->getKey(),
    ]);
});

it('can update a classroom', function () {
    $classroom = Classroom::factory()
        ->for(createActiveAcademicYear(), 'academicYear')
        ->create(['name' => 'IX A']);

    Livewire::test(EditClassroom::class, ['record' => $classroom->getKey()])
        ->fillForm([
            'academic_year_id' => $classroom->academic_year_id,
            'teacher_id' => $classroom->teacher_id,
            'name' => 'IX B',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($classroom->refresh()->name)->toEqual('IX B');
});

it('can view classroom details', function () {
    $classroom = Classroom::factory()
        ->for(createActiveAcademicYear(), 'academicYear')
        ->create(['name' => 'VII B']);

    Livewire::test(ViewClassroom::class, ['record' => $classroom->getKey()])
        ->assertSet('record.name', 'VII B')
        ->assertActionExists('edit');
});

it('can delete classrooms via bulk action', function () {
    $classrooms = Classroom::factory()
        ->count(2)
        ->for(createActiveAcademicYear(), 'academicYear')
        ->create();

    Livewire::test(ListClassrooms::class)
        ->callTableBulkAction('delete', $classrooms->pluck('id')->all());

    expect(Classroom::whereKey($classrooms->pluck('id'))->exists())->toBeFalse();
});

it('exposes student creation action in relation manager', function () {
    $classroom = Classroom::factory()
        ->for(createActiveAcademicYear(), 'academicYear')
        ->create();

    Livewire::test(StudentsRelationManager::class, [
        'ownerRecord' => $classroom,
        'pageClass' => ViewClassroom::class,
    ])
        ->assertTableActionExists('create');
});

it('can create presences from relation manager', function () {
    $classroom = createClassroomWithStudents(2);

    Livewire::test(PresencesRelationManager::class, [
        'ownerRecord' => $classroom,
        'pageClass' => ViewClassroom::class,
    ])
        ->callTableAction('create', data: [
            'date' => now()->toDateString(),
        ])
        ->assertHasNoTableActionErrors();

    expect(Presence::where('classroom_id', $classroom->getKey())->count())->toBe(1);
});

it('can manage presence record actions from relation manager', function () {
    $classroom = createClassroomWithStudents(2);
    $presence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);

    // View
    Livewire::test(PresencesRelationManager::class, [
        'ownerRecord' => $classroom->refresh(),
        'pageClass' => ViewClassroom::class,
    ])
        ->assertTableActionExists('view', null, $presence);

    // Edit
    Livewire::test(PresencesRelationManager::class, [
        'ownerRecord' => $classroom->refresh(),
        'pageClass' => ViewClassroom::class,
    ])
        ->callTableAction('edit', $presence->getKey(), data: [
            'date' => now()->addDay()->toDateString(),
        ])
        ->assertHasNoTableActionErrors();

    expect($presence->refresh()->date)->toEqual(now()->addDay()->toDateString());

    $presence->studentPresences()->delete();

    // Delete
    Livewire::test(PresencesRelationManager::class, [
        'ownerRecord' => $classroom->refresh(),
        'pageClass' => ViewClassroom::class,
    ])
        ->callTableAction('delete', $presence->getKey())
        ->assertHasNoTableActionErrors();

    expect(Presence::whereKey($presence->getKey())->exists())->toBeFalse();
});

it('can create assessments from relation manager', function () {
    $classroom = createClassroomWithStudents(2);
    $subject = Subject::factory()->create();

    Livewire::test(AssesmentsRelationManager::class, [
        'ownerRecord' => $classroom,
        'pageClass' => ViewClassroom::class,
    ])
        ->callTableAction('create', data: [
            'subject_id' => $subject->getKey(),
            'type' => AssesmentType::MIDTERM->value ?? AssesmentType::cases()[0]->value,
        ])
        ->assertHasNoTableActionErrors();

    expect(Assesment::where('classroom_id', $classroom->getKey())->exists())->toBeTrue();
});

it('can manage assessment record actions from relation manager', function () {
    $classroom = createClassroomWithStudents(2);
    $subject = Subject::factory()->create();
    $assesment = Assesment::factory()
        ->for($classroom, 'classroom')
        ->for($subject, 'subject')
        ->create(['type' => AssesmentType::QUIZ]);

    // View
    Livewire::test(AssesmentsRelationManager::class, [
        'ownerRecord' => $classroom,
        'pageClass' => ViewClassroom::class,
    ])
        ->assertTableActionExists('view', null, $assesment);

    // Edit
    Livewire::test(AssesmentsRelationManager::class, [
        'ownerRecord' => $classroom,
        'pageClass' => ViewClassroom::class,
    ])
        ->callTableAction('edit', $assesment->getKey(), data: [
            'subject_id' => $subject->getKey(),
            'type' => AssesmentType::FINAL->value ?? AssesmentType::cases()[0]->value,
        ])
        ->assertHasNoTableActionErrors();

    expect($assesment->refresh()->type->value)->toEqual(
        AssesmentType::FINAL->value ?? AssesmentType::cases()[0]->value,
    );
});
