<?php

use App\Filament\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Resources\Teachers\Pages\ListTeachers;
use App\Filament\Resources\Teachers\Pages\ViewTeacher;
use App\Filament\Resources\Teachers\RelationManagers\ClassroomsRelationManager;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render teacher list page', function () {
    $this->get(ListTeachers::getUrl())
        ->assertOk();
});

it('lists teachers in the table', function () {
    $teacher = Teacher::factory()->create(['name' => 'Guru Favorit']);

    Livewire::test(ListTeachers::class)
        ->assertCanSeeTableRecords([$teacher])
        ->assertActionExists('create');
});

it('can create a teacher', function () {
    $payload = [
        'name' => 'Guru Baru',
        'email' => 'guru-baru@example.com',
        'password' => 'PasswordTeacher123!',
    ];

    Livewire::test(CreateTeacher::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $teacherUser = User::query()->where('email', $payload['email'])->first();

    expect($teacherUser)->not()->toBeNull();
    expect(Hash::check($payload['password'], $teacherUser->password))->toBeTrue();
});

it('can update a teacher', function () {
    $teacher = Teacher::factory()->create([
        'name' => 'Guru Lama',
        'email' => 'guru-lama@example.com',
    ]);

    $updates = [
        'name' => 'Guru Terbaru',
        'email' => 'guru-terbaru@example.com',
        'password' => 'PasswordBaruTeacher456!',
    ];

    Livewire::test(EditTeacher::class, ['record' => $teacher->getKey()])
        ->fillForm($updates)
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $teacher->refresh();

    expect($fresh->name)->toEqual('Guru Terbaru');
    expect($fresh->email)->toEqual('guru-terbaru@example.com');
    expect(Hash::check($updates['password'], $fresh->password))->toBeTrue();
});

it('can view teacher details', function () {
    $teacher = Teacher::factory()->create(['email' => 'teacher-view@example.com']);

    Livewire::test(ViewTeacher::class, ['record' => $teacher->getKey()])
        ->assertSet('record.email', 'teacher-view@example.com')
        ->assertActionExists('edit');
});

it('exposes classrooms relation while keeping it read only', function () {
    $teacher = Teacher::factory()->create();

    $classroom = Classroom::factory()
        ->for(createActiveAcademicYear(), 'academicYear')
        ->state(['teacher_id' => $teacher->getKey()])
        ->create();

    Livewire::test(ClassroomsRelationManager::class, [
        'ownerRecord' => $teacher,
        'pageClass' => ViewTeacher::class,
    ])
        ->assertTableActionExists('create')
        ->assertCanSeeTableRecords([$classroom]);
});

it('can delete teachers via bulk action', function () {
    $teachers = Teacher::factory()->count(2)->create();

    Livewire::test(ListTeachers::class)
        ->callTableBulkAction('delete', $teachers->pluck('id')->all());

    expect(Teacher::whereKey($teachers->pluck('id'))->exists())->toBeFalse();
});
