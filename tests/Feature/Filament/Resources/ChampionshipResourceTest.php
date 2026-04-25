<?php

use App\Enums\ChampionLevel;
use App\Enums\ChampionType;
use App\Filament\Resources\Championships\Pages\CreateChampionship;
use App\Filament\Resources\Championships\Pages\EditChampionship;
use App\Filament\Resources\Championships\Pages\ListChampionships;
use App\Filament\Resources\Championships\Pages\ViewChampionship;
use App\Models\Championship;
use App\Models\Student;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render championship list page', function () {
    $this->get(ListChampionships::getUrl())
        ->assertOk();
});

it('lists championships in the table', function () {
    $championship = Championship::factory()->create([
        'name' => 'Olimpiade Sains',
        'level' => ChampionLevel::NATIONAL,
        'type' => ChampionType::INDIVIDUAL,
    ]);

    Livewire::test(ListChampionships::class)
        ->assertCanSeeTableRecords([$championship])
        ->assertActionExists('create');
});

it('can create a championship record', function () {
    $student = Student::factory()->create();

    $payload = [
        'student_id' => $student->getKey(),
        'name' => 'Kompetisi Matematika',
        'year' => '2024-01-01',
        'level' => ChampionLevel::NATIONAL->value,
        'position' => 'Juara 1',
        'type' => ChampionType::INDIVIDUAL->value,
    ];

    Livewire::test(CreateChampionship::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('championships', [
        'student_id' => $student->getKey(),
        'name' => 'Kompetisi Matematika',
    ]);
});

it('can update a championship', function () {
    $championship = Championship::factory()->create([
        'position' => 'Juara 2',
        'level' => ChampionLevel::SCHOOL,
        'type' => ChampionType::INDIVIDUAL,
    ]);

    Livewire::test(EditChampionship::class, ['record' => $championship->getKey()])
        ->fillForm([
            'student_id' => $championship->student_id,
            'name' => 'Kompetisi Sains Terapan',
            'year' => now()->toDateString(),
            'level' => ChampionLevel::PROVINCIAL->value,
            'position' => 'Juara 1',
            'type' => ChampionType::TEAM->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $championship->refresh();

    expect($fresh->name)->toEqual('Kompetisi Sains Terapan');
    expect($fresh->position)->toEqual('Juara 1');
    expect($fresh->type->value)->toEqual(ChampionType::TEAM->value);
});

it('can view championship details', function () {
    $championship = Championship::factory()->create([
        'name' => 'Festival Seni',
        'level' => ChampionLevel::DISTRICT,
        'type' => ChampionType::TEAM,
    ]);

    Livewire::test(ViewChampionship::class, ['record' => $championship->getKey()])
        ->assertSet('record.name', 'Festival Seni')
        ->assertActionExists('edit');
});

it('can delete championships via bulk action', function () {
    $championships = Championship::factory()->count(2)->create([
        'level' => ChampionLevel::SCHOOL,
        'type' => ChampionType::INDIVIDUAL,
    ]);

    Livewire::test(ListChampionships::class)
        ->callTableBulkAction('delete', $championships->pluck('id')->all());

    expect(Championship::whereKey($championships->pluck('id'))->exists())->toBeFalse();
});
