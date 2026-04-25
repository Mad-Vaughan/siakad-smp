<?php

use App\Enums\PresenceStatus;
use App\Filament\Resources\Presences\Pages\CreatePresence;
use App\Filament\Resources\Presences\Pages\EditPresence;
use App\Filament\Resources\Presences\Pages\ListPresences;
use App\Filament\Resources\Presences\Pages\ViewPresence;
use App\Filament\Resources\Presences\RelationManagers\StudentPresencesRelationManager;
use App\Models\Presence;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render presence list page', function () {
    $this->get(ListPresences::getUrl())
        ->assertOk();
});

it('shows presences in the table', function () {
    $classroom = createClassroomWithStudents(2);
    $presence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);

    Livewire::test(ListPresences::class)
        ->assertCanSeeTableRecords([$presence])
        ->assertActionExists('create');
});

it('can create a presence', function () {
    $classroom = createClassroomWithStudents(2);

    $payload = [
        'classroom_id' => $classroom->getKey(),
        'date' => now()->toDateString(),
    ];

    Livewire::test(CreatePresence::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $presence = Presence::query()
        ->where('classroom_id', $classroom->getKey())
        ->whereDate('date', now()->toDateString())
        ->first();

    expect($presence)->not()->toBeNull();
    expect($presence->studentPresences)->toHaveCount($classroom->students()->count());
});

it('can update a presence', function () {
    $classroom = createClassroomWithStudents(2);
    $presence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);

    Livewire::test(EditPresence::class, ['record' => $presence->getKey()])
        ->fillForm([
            'classroom_id' => $classroom->getKey(),
            'date' => now()->addDay()->toDateString(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($presence->refresh()->date)->toEqual(now()->addDay()->toDateString());
});

it('can view presence details', function () {
    $classroom = createClassroomWithStudents(1);
    $presence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);

    Livewire::test(ViewPresence::class, ['record' => $presence->getKey()])
        ->assertSet('record.date', $presence->date)
        ->assertActionExists('edit');
});

it('can download presence pdf report', function () {
    $classroom = createClassroomWithStudents(1);
    $classroom->update(['name' => 'Kelas 7A']);
    $presence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);

    $expectedFileName = sprintf(
        'presensi-%s-%s.pdf',
        Str::slug($classroom->fresh()->name),
        \Illuminate\Support\Carbon::parse($presence->date)->format('Ymd'),
    );

    Livewire::test(ViewPresence::class, ['record' => $presence->getKey()])
        ->callAction('printPdf')
        ->assertFileDownloaded($expectedFileName);
});

it('can delete presences via bulk action', function () {
    $classroom = createClassroomWithStudents(1);
    $presences = Presence::factory()
        ->count(2)
        ->create([
            'classroom_id' => $classroom->getKey(),
            'academic_year_id' => $classroom->academic_year_id,
            'date' => now()->toDateString(),
        ]);

    $presences->each(fn ($presence) => $presence->studentPresences()->delete());

    Livewire::test(ListPresences::class)
        ->callTableBulkAction('delete', $presences->pluck('id')->all());

    expect(Presence::whereKey($presences->pluck('id'))->exists())->toBeFalse();
});

it('can update student presence statuses from relation manager', function () {
    $classroom = createClassroomWithStudents(1);
    $presence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);
    $studentPresence = $presence->studentPresences()->first();

    Livewire::test(StudentPresencesRelationManager::class, [
        'ownerRecord' => $presence->refresh(),
        'pageClass' => ViewPresence::class,
    ])
        ->call('updatePresenceStatus', $studentPresence->getKey(), PresenceStatus::SICK->value);

    expect($studentPresence->refresh()->status->value)->toEqual(PresenceStatus::SICK->value);
});

it('can edit student presence notes inline', function () {
    $classroom = createClassroomWithStudents(1);
    $presence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);
    $studentPresence = $presence->studentPresences()->first();

    Livewire::test(StudentPresencesRelationManager::class, [
        'ownerRecord' => $presence->refresh(),
        'pageClass' => ViewPresence::class,
    ])
        ->call('updateTableColumnState', 'note', (string) $studentPresence->getKey(), 'Datang terlambat');

    expect($studentPresence->refresh()->note)->toEqual('Datang terlambat');
});

it('sends notification when saving student presences', function () {
    $classroom = createClassroomWithStudents(1);
    $presence = createPresenceForClassroom($classroom, ['date' => now()->toDateString()]);

    session()->forget('filament.notifications');

    Livewire::test(StudentPresencesRelationManager::class, [
        'ownerRecord' => $presence->refresh(),
        'pageClass' => ViewPresence::class,
    ])
        ->callTableAction('save')
        ->assertNotified('Berhasil menyimpan presensi siswa.');
});
