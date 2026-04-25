<?php

use App\Filament\Pages\ManageLetterhead;
use App\Settings\LetterheadSetting;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function () {
    loginAsAdmin();
    enableIconFallback();
});

it('provides fallback values when settings are missing', function () {
    DB::table('settings')->where('group', 'letterhead')->delete();
    app()->forgetInstance(LetterheadSetting::class);

    $settings = LetterheadSetting::resolveWithFallback();

    expect($settings->organization_name)->toEqual('SMP MUARA INDONESIA');
    expect($settings->organization_tagline)->toEqual('Unggul dalam Prestasi dan Akhlak');
});

it('can update letterhead settings via settings page', function () {
    $payload = [
        'organization_name' => 'SMP Negeri 1',
        'organization_tagline' => 'Berprestasi dan Berakhlak',
        'phone' => '0812-1234-5678',
        'email' => 'humas@smpn1.test',
    ];

    Livewire::test(ManageLetterhead::class)
        ->set('data.organization_name', $payload['organization_name'])
        ->set('data.organization_tagline', $payload['organization_tagline'])
        ->set('data.phone', $payload['phone'])
        ->set('data.email', $payload['email'])
        ->call('save');

    $settings = app(LetterheadSetting::class);

    expect($settings->organization_name)->toEqual($payload['organization_name']);
    expect($settings->organization_tagline)->toEqual($payload['organization_tagline']);
    expect($settings->phone)->toEqual($payload['phone']);
    expect($settings->email)->toEqual($payload['email']);
});
