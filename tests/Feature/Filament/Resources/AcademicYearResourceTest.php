<?php

use App\Filament\Resources\AcademicYears\Pages\CreateAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\EditAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\ListAcademicYears;
use App\Filament\Resources\AcademicYears\Pages\ViewAcademicYear;
use App\Models\AcademicYear;

use function Pest\Livewire\livewire;

beforeEach(function () {
    loginAsAdmin();
});

it('can render academic year list page', function () {
    $this->get(ListAcademicYears::getUrl())
        ->assertOk();
});

it('can list academic years in the table', function () {
    $academicYear = AcademicYear::factory()->create(['name' => '2024/2025']);

    livewire(ListAcademicYears::class)
        ->assertCanSeeTableRecords([$academicYear]);
});

it('can create an academic year and deactivate old active year', function () {
    $existingActive = AcademicYear::factory()->create(['is_active' => true]);

    $payload = [
        'name' => '2026/2027',
        'start_date' => now()->startOfYear()->toDateString(),
        'end_date' => now()->endOfYear()->toDateString(),
        'is_active' => true,
    ];

    livewire(CreateAcademicYear::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('academic_years', [
        'name' => '2026/2027',
        'is_active' => true,
    ]);
});

it('can update an academic year and ensure exclusivity of active status', function () {
    $inactive = AcademicYear::factory()->create([
        'name' => '2023/2024',
        'is_active' => false,
    ]);

    $active = AcademicYear::factory()->create([
        'name' => '2024/2025',
        'is_active' => true,
    ]);

    $updates = [
        'name' => '2025/2026',
        'start_date' => now()->addYear()->startOfYear()->toDateString(),
        'end_date' => now()->addYear()->endOfYear()->toDateString(),
        'is_active' => true,
    ];

    livewire(EditAcademicYear::class, ['record' => $inactive->getKey()])
        ->fillForm($updates)
        ->call('save')
        ->assertHasNoFormErrors();
});

it('can delete academic years via bulk action', function () {
    $years = AcademicYear::factory()->count(2)->create();

    livewire(ListAcademicYears::class)
        ->callTableBulkAction('delete', $years->pluck('id')->all());

    expect(AcademicYear::whereKey($years->pluck('id'))->exists())->toBeFalse();
});

it('can view academic year details', function () {
    $academicYear = AcademicYear::factory()->create(['name' => 'Tahun Ajaran Khusus']);

    livewire(ViewAcademicYear::class, ['record' => $academicYear->getKey()])
        ->assertSet('record.name', 'Tahun Ajaran Khusus')
        ->assertActionExists('edit');
});
