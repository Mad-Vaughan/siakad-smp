<?php

use App\Enums\PresenceStatus;
use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Filament\Resources\Students\Pages\ViewStudent;
use App\Filament\Resources\Students\RelationManagers\ChampionshipsRelationManager;
use App\Filament\Resources\Students\RelationManagers\StudentPresencesRelationManager;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render student list page', function () {
    $this->get(ListStudents::getUrl())
        ->assertOk();
});

it('shows student records in the table', function () {
    $student = Student::factory()->create(['name' => 'Siswa Unggulan']);

    Livewire::test(ListStudents::class)
        ->assertCanSeeTableRecords([$student])
        ->assertActionExists('create');
});

it('can create a student', function () {
    $payload = [
        'name' => 'Siswa Baru',
        'nisn' => Str::random(10),
        'date_of_birth' => '2008-05-15',
        'address' => 'Alamat Lengkap',
    ];

    Livewire::test(CreateStudent::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $studentUser = User::query()->where('nisn', $payload['nisn'])->first();

    expect($studentUser)->not()->toBeNull();
    expect($studentUser->address)->toEqual('Alamat Lengkap');
    expect($studentUser->date_of_birth)->toEqual('2008-05-15');
});

it('can update a student', function () {
    $student = Student::factory()->create([
        'name' => 'Siswa Lama',
    ]);

    $updates = [
        'name' => 'Siswa Perbaruan',
    ];

    Livewire::test(EditStudent::class, ['record' => $student->getKey()])
        ->fillForm($updates)
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $student->refresh();

    expect($fresh->name)->toEqual('Siswa Perbaruan');
});

it('can view student details', function () {
    $student = Student::factory()->create();

    Livewire::test(ViewStudent::class, ['record' => $student->getKey()])
        ->assertActionExists('edit');
});

it('exposes championships relation manager actions', function () {
    $student = Student::factory()->create();

    Livewire::test(ChampionshipsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertTableActionExists('create');
});

it('can filter student presences by status', function () {
    $classroom = createClassroomWithStudents(1);

    /** @var Student $student */
    $student = $classroom->studentClassrooms()->with('student')->first()->student;

    $firstPresence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);
    $secondPresence = createPresenceForClassroom($classroom, ['date' => now()->addDay()->toDateString()]);

    $absenceRecord = $student->studentPresences()->where('presence_id', $firstPresence->id)->first();
    $presentRecord = $student->studentPresences()->where('presence_id', $secondPresence->id)->first();

    $absenceRecord->update(['status' => PresenceStatus::ABSENT]);
    $presentRecord->update(['status' => PresenceStatus::PRESENT]);

    Livewire::test(StudentPresencesRelationManager::class, [
        'ownerRecord' => $student->refresh(),
        'pageClass' => ViewStudent::class,
    ])
        ->assertCanSeeTableRecords([$absenceRecord->refresh(), $presentRecord->refresh()])
        ->filterTable('status', PresenceStatus::ABSENT->value)
        ->assertCanSeeTableRecords([$absenceRecord->refresh()])
        ->assertCanNotSeeTableRecords([$presentRecord->refresh()]);
});

it('can delete students via bulk action', function () {
    $students = Student::factory()->count(2)->create();

    Livewire::test(ListStudents::class)
        ->callTableBulkAction('delete', $students->pluck('id')->all());

    expect(Student::whereKey($students->pluck('id'))->exists())->toBeFalse();
});
