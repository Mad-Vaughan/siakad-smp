<?php

use App\Filament\Resources\Subjects\Pages\CreateSubject;
use App\Filament\Resources\Subjects\Pages\EditSubject;
use App\Filament\Resources\Subjects\Pages\ListSubjects;
use App\Filament\Resources\Subjects\Pages\ViewSubject;
use App\Models\Subject;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render subject list page', function () {
    $this->get(ListSubjects::getUrl())
        ->assertOk();
});

it('shows subject records in the table', function () {
    $subject = Subject::factory()->create(['name' => 'Matematika']);

    Livewire::test(ListSubjects::class)
        ->assertCanSeeTableRecords([$subject])
        ->assertActionExists('create');
});

it('can create a subject', function () {
    $payload = [
        'name' => 'Bahasa Indonesia',
    ];

    Livewire::test(CreateSubject::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('subjects', $payload);
});

it('can update a subject', function () {
    $subject = Subject::factory()->create(['name' => 'IPA']);

    Livewire::test(EditSubject::class, ['record' => $subject->getKey()])
        ->fillForm(['name' => 'Ilmu Pengetahuan Alam'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($subject->refresh()->name)->toEqual('Ilmu Pengetahuan Alam');
});

it('can view subject details', function () {
    $subject = Subject::factory()->create(['name' => 'IPS']);

    Livewire::test(ViewSubject::class, ['record' => $subject->getKey()])
        ->assertSet('record.name', 'IPS')
        ->assertActionExists('edit');
});

it('can delete subjects via bulk action', function () {
    $subjects = Subject::factory()->count(2)->create();

    Livewire::test(ListSubjects::class)
        ->callTableBulkAction('delete', $subjects->pluck('id')->all());

    expect(Subject::whereKey($subjects->pluck('id'))->exists())->toBeFalse();
});
