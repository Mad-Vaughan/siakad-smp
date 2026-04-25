<?php

use App\Filament\Resources\Admins\Pages\CreateAdmin;
use App\Filament\Resources\Admins\Pages\EditAdmin;
use App\Filament\Resources\Admins\Pages\ListAdmins;
use App\Filament\Resources\Admins\Pages\ViewAdmin;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('can render admin list page', function () {
    $this->get(ListAdmins::getUrl())
        ->assertOk();
});

it('shows admin records in the table', function () {
    $admin = Admin::factory()->create(['name' => 'Admin Example']);

    Livewire::test(ListAdmins::class)
        ->assertCanSeeTableRecords([$admin])
        ->assertActionExists('create');
});

it('can create an admin', function () {
    $payload = [
        'name' => 'New Admin',
        'email' => 'new-admin@example.com',
        'password' => 'Password123!',
    ];

    Livewire::test(CreateAdmin::class)
        ->fillForm($payload)
        ->call('create')
        ->assertHasNoFormErrors();

    $createdUser = User::query()->where('email', $payload['email'])->first();

    expect($createdUser)->not()->toBeNull();
    expect(Hash::check($payload['password'], $createdUser->password))->toBeTrue();
});

it('can update an admin', function () {
    $admin = Admin::factory()->create([
        'name' => 'Initial Admin',
        'email' => 'initial@example.com',
    ]);

    $updates = [
        'name' => 'Updated Admin',
        'email' => 'updated@example.com',
        'password' => 'UpdatedPassword123!',
    ];

    Livewire::test(EditAdmin::class, ['record' => $admin->getKey()])
        ->fillForm($updates)
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $admin->refresh();

    expect($fresh->name)->toEqual('Updated Admin');
    expect($fresh->email)->toEqual('updated@example.com');
    expect(Hash::check($updates['password'], $fresh->password))->toBeTrue();
});

it('can view admin details', function () {
    $admin = Admin::factory()->create(['email' => 'view@example.com']);

    Livewire::test(ViewAdmin::class, ['record' => $admin->getKey()])
        ->assertSet('record.email', 'view@example.com')
        ->assertActionExists('edit');
});

it('can delete admins via bulk action', function () {
    $admins = Admin::factory()->count(2)->create();

    Livewire::test(ListAdmins::class)
        ->callTableBulkAction('delete', $admins->pluck('id')->all());

    expect(Admin::whereKey($admins->pluck('id'))->exists())->toBeFalse();
});
