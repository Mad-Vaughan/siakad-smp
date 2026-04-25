<?php

namespace App\Filament\Pages;

use App\Settings\LetterheadSetting;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageLetterhead extends SettingsPage
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string $settings = LetterheadSetting::class;

    protected static ?string $title = 'Pengaturan Surat';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 110;

    public static function getNavigationLabel(): string
    {
        return 'Pengaturan Surat';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Identitas')
                        ->icon(Heroicon::OutlinedBuildingOffice)
                        ->schema([
                            TextInput::make('organization_name')
                                ->label('Nama Instansi')
                                ->placeholder('SMP Muara Indonesia')
                                ->required(),
                            TextInput::make('organization_tagline')
                                ->label('Tagline / Subjudul')
                                ->placeholder('Unggul dalam Prestasi & Akhlak'),
                            FileUpload::make('logo')
                                ->label('Logo')
                                ->image()
                                ->directory('letterhead')
                                ->nullable(),
                        ]),
                    Tab::make('Kontak')
                        ->icon(Heroicon::OutlinedPhone)
                        ->schema([
                            Textarea::make('address')
                                ->label('Alamat')
                                ->rows(3)
                                ->placeholder('Jalan Kemerdekaan No. 10, Medan'),
                            TextInput::make('phone')
                                ->label('Telepon')
                                ->tel()
                                ->placeholder('0812-3456-7890'),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->placeholder('info@sekolah.test'),
                            TextInput::make('website')
                                ->label('Website')
                                ->placeholder('https://sekolah.test'),
                        ]),
                    Tab::make('Catatan')
                        ->icon(Heroicon::OutlinedClipboardDocumentList)
                        ->schema([
                            Textarea::make('footer_note')
                                ->label('Catatan Penutup')
                                ->rows(3)
                                ->placeholder('Dokumen ini dicetak secara otomatis oleh sistem.'),
                        ]),
                ]),
        ]);
    }
}
