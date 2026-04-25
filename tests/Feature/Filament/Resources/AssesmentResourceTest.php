<?php

use App\Enums\AssesmentType;
use App\Filament\Resources\Assesments\Pages\CreateAssesment;
use App\Filament\Resources\Assesments\Pages\EditAssesment;
use App\Filament\Resources\Assesments\Pages\ListAssesments;
use App\Filament\Resources\Assesments\Pages\ViewAssesment;
use App\Filament\Resources\Assesments\RelationManagers\StudentAssesmentsRelationManager;
use App\Models\Assesment;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render assessment list page', function () {
    $this->get(ListAssesments::getUrl())
        ->assertOk();
});

it('shows assessments in the table', function () {
    $assesment = Assesment::factory()->create();

    $this->get(ListAssesments::getUrl())
        ->assertOk()
        ->assertSee($assesment->type->getLabel());
});

it('can create an assessment and generate student assessment records', function () {
    $classroom = createClassroomWithStudents(2);
    $subject = Subject::factory()->create();

    $payload = [
        'classroom_id' => $classroom->getKey(),
        'subject_id' => $subject->getKey(),
        'type' => AssesmentType::QUIZ->value,
    ];

    Livewire::test(CreateAssesment::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $assesment = Assesment::query()
        ->where('classroom_id', $classroom->getKey())
        ->where('subject_id', $subject->getKey())
        ->first();

    expect($assesment)->not()->toBeNull();
    expect($assesment->studentAssesments()->count())->toEqual($classroom->students()->count());
});

it('can update an assessment', function () {
    $classroom = createClassroomWithStudents(1);
    $subject = Subject::factory()->create();
    $assesment = Assesment::factory()
        ->for($classroom, 'classroom')
        ->for($subject, 'subject')
        ->create(['type' => AssesmentType::QUIZ]);

    $updates = [
        'classroom_id' => $classroom->getKey(),
        'subject_id' => $subject->getKey(),
        'type' => AssesmentType::FINAL->value,
    ];

    Livewire::test(EditAssesment::class, ['record' => $assesment->getKey()])
        ->fillForm($updates)
        ->call('save')
        ->assertHasNoFormErrors();

    expect($assesment->refresh()->type->value)->toEqual(AssesmentType::FINAL->value);
});

it('can view assessment details', function () {
    $assesment = Assesment::factory()->create(['type' => AssesmentType::EXAM]);

    Livewire::test(ViewAssesment::class, ['record' => $assesment->getKey()])
        ->assertSet('record.type', fn ($value) => $value === AssesmentType::EXAM)
        ->assertActionExists('edit');
});

it('can download assessment pdf report', function () {
    $classroom = createClassroomWithStudents(1);
    $classroom->update(['name' => 'Kelas 8A']);
    $subject = Subject::factory()->create(['name' => 'Matematika']);
    $assesment = Assesment::factory()
        ->for($classroom, 'classroom')
        ->for($subject, 'subject')
        ->create(['type' => AssesmentType::QUIZ]);

    Carbon::setTestNow($now = now());

    $expectedFileName = sprintf(
        'penilaian-%s-%s-%s.pdf',
        Str::slug($subject->name),
        Str::slug($classroom->name),
        $now->format('YmdHis'),
    );

    Livewire::test(ViewAssesment::class, ['record' => $assesment->getKey()])
        ->callAction('printScores')
        ->assertFileDownloaded($expectedFileName);

    Carbon::setTestNow();
});

it('can delete assessments via bulk action', function () {
    $assesments = Assesment::factory()->count(2)->create();

    Livewire::test(ListAssesments::class)
        ->callTableBulkAction('delete', $assesments->pluck('id')->all());

    expect(Assesment::whereKey($assesments->pluck('id'))->exists())->toBeFalse();
});

it('can update student assessment scores inline', function () {
    $classroom = createClassroomWithStudents(1);
    $subject = Subject::factory()->create();
    $assesment = Assesment::factory()
        ->for($classroom, 'classroom')
        ->for($subject, 'subject')
        ->create(['type' => AssesmentType::QUIZ]);

    $studentAssesment = $assesment->studentAssesments()->first();

    Livewire::test(StudentAssesmentsRelationManager::class, [
        'ownerRecord' => $assesment->refresh(),
        'pageClass' => ViewAssesment::class,
    ])
        ->call('updateTableColumnState', 'score', (string) $studentAssesment->getKey(), '88');

    expect($studentAssesment->refresh()->score)->toEqual(88.0);
});

it('validates student assessment scores within accepted range', function () {
    $classroom = createClassroomWithStudents(1);
    $subject = Subject::factory()->create();
    $assesment = Assesment::factory()
        ->for($classroom, 'classroom')
        ->for($subject, 'subject')
        ->create(['type' => AssesmentType::QUIZ]);

    $studentAssesment = $assesment->studentAssesments()->first();

    $component = Livewire::test(StudentAssesmentsRelationManager::class, [
        'ownerRecord' => $assesment->refresh(),
        'pageClass' => ViewAssesment::class,
    ]);

    $component->call('updateTableColumnState', 'score', (string) $studentAssesment->getKey(), '105');

    expect($studentAssesment->refresh()->score)->toBeNull();
});

it('notifies when saving student assessments from relation manager', function () {
    $classroom = createClassroomWithStudents(1);
    $subject = Subject::factory()->create();
    $assesment = Assesment::factory()
        ->for($classroom, 'classroom')
        ->for($subject, 'subject')
        ->create(['type' => AssesmentType::QUIZ]);

    session()->forget('filament.notifications');

    Livewire::test(StudentAssesmentsRelationManager::class, [
        'ownerRecord' => $assesment->refresh(),
        'pageClass' => ViewAssesment::class,
    ])
        ->callTableAction('save')
        ->assertNotified('Berhasil menyimpan nilai siswa.');
});
