<?php

namespace App\Filament\Pages;

use App\Settings\WebsiteSetting;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageWebsite extends SettingsPage
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = WebsiteSetting::class;

    protected static ?string $title = 'Kelola Website';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 100;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Identitas')
                        ->icon(Heroicon::OutlinedBuildingOffice)
                        ->schema([
                            TextInput::make('site_name')
                                ->label('Nama Situs')
                                ->placeholder('Masukkan nama situs')
                                ->required(),
                            FileUpload::make('site_logo')
                                ->label('Logo Situs')
                                ->image()
                                ->directory('website')
                                ->nullable(),
                            TextInput::make('email')
                                ->label('Email')
                                ->placeholder('Masukkan email resmi')
                                ->email(),
                            TextInput::make('phone')
                                ->label('Nomor Telepon')
                                ->placeholder('Masukkan nomor telepon')
                                ->tel(),
                            Textarea::make('address')
                                ->label('Alamat Lengkap')
                                ->placeholder('Masukkan alamat lengkap')
                                ->rows(3),
                        ]),
                    Tab::make('Branding')
                        ->icon(Heroicon::OutlinedSwatch)
                        ->schema([
                            ColorPicker::make('theme_primary_color')
                                ->label('Warna Primer')
                                ->required(),
                            ColorPicker::make('theme_secondary_color')
                                ->label('Warna Sekunder')
                                ->required(),
                            ColorPicker::make('theme_accent_color')
                                ->label('Warna Aksen')
                                ->required(),
                            ColorPicker::make('theme_background_color')
                                ->label('Warna Latar')
                                ->required(),
                            ColorPicker::make('theme_text_color')
                                ->label('Warna Teks')
                                ->required(),
                        ]),
                    Tab::make('Hero')
                        ->icon(Heroicon::OutlinedSparkles)
                        ->schema([
                            TextInput::make('hero_tagline')
                                ->label('Tagline Singkat')
                                ->placeholder('Sekolah Ramah, Prestasi Gemilang')
                                ->required(),
                            TextInput::make('hero_title')
                                ->label('Judul Utama')
                                ->placeholder('Membuka Peluang Masa Depan...')
                                ->required(),
                            Textarea::make('hero_description')
                                ->label('Deskripsi Hero')
                                ->rows(4)
                                ->placeholder('Tulis narasi singkat mengenai sekolah')
                                ->required(),
                            TextInput::make('hero_primary_cta_label')
                                ->label('Teks Tombol Utama')
                                ->placeholder('Daftar PPDB')
                                ->required(),
                            TextInput::make('hero_primary_cta_url')
                                ->label('Tautan Tombol Utama')
                                ->placeholder('/ppdb')
                                ->required(),
                            TextInput::make('hero_secondary_cta_url')
                                ->label('Tautan Tombol Sekunder')
                                ->placeholder('/hubungi-kami'),
                            FileUpload::make('hero_media')
                                ->label('Gambar Hero')
                                ->image()
                                ->directory('website/hero')
                                ->nullable(),
                            TagsInput::make('hero_highlights')
                                ->label('Sorotan Singkat')
                                ->placeholder('Masukkan sorotan, tekan enter untuk menambah')
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Statistik & Sorotan')
                        ->icon(Heroicon::OutlinedPresentationChartLine)
                        ->schema([
                            Repeater::make('highlight_stats')
                                ->label('Statistik Utama')
                                ->minItems(1)
                                ->schema([
                                    TextInput::make('value')
                                        ->label('Nilai')
                                        ->required(),
                                    TextInput::make('label')
                                        ->label('Label')
                                        ->required(),
                                    Textarea::make('description')
                                        ->label('Deskripsi')
                                        ->rows(2),
                                ]),
                            Repeater::make('excellence_features')
                                ->label('Keunggulan Sekolah')
                                ->minItems(1)
                                ->schema([
                                    TextInput::make('title')
                                        ->label('Judul')
                                        ->required(),
                                    Textarea::make('description')
                                        ->label('Deskripsi')
                                        ->rows(3),
                                ]),
                        ]),
                    Tab::make('Program & Kegiatan')
                        ->icon(Heroicon::OutlinedAcademicCap)
                        ->schema([
                            Repeater::make('program_cards')
                                ->label('Program Unggulan')
                                ->minItems(1)
                                ->schema([
                                    TextInput::make('title')
                                        ->label('Judul Program')
                                        ->required(),
                                    Textarea::make('description')
                                        ->label('Deskripsi')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    TextInput::make('link_label')
                                        ->label('Teks Tautan')
                                        ->placeholder('Pelajari Program'),
                                    TextInput::make('link_url')
                                        ->label('URL / Route')
                                        ->placeholder('/program'),
                                ]),
                        ]),
                    Tab::make('Testimoni & CTA')
                        ->icon(Heroicon::OutlinedMegaphone)
                        ->schema([
                            Repeater::make('testimonials')
                                ->label('Testimoni')
                                ->schema([
                                    Textarea::make('quote')
                                        ->label('Kutipan')
                                        ->rows(3)
                                        ->required(),
                                    TextInput::make('name')
                                        ->label('Nama')
                                        ->required(),
                                    TextInput::make('role')
                                        ->label('Peran / Angkatan'),
                                ]),
                            TextInput::make('cta_title')
                                ->label('Judul CTA')
                                ->placeholder('Siap bergabung?')
                                ->required(),
                            Textarea::make('cta_description')
                                ->label('Deskripsi CTA')
                                ->rows(3)
                                ->required(),
                            TextInput::make('cta_button_label')
                                ->label('Teks Tombol CTA')
                                ->placeholder('Hubungi Kami')
                                ->required(),
                            TextInput::make('cta_button_url')
                                ->label('Tautan Tombol CTA')
                                ->placeholder('/ppdb')
                                ->required(),
                        ]),
                    Tab::make('Footer & Sosial')
                        ->icon(Heroicon::OutlinedGlobeAmericas)
                        ->schema([
                            Textarea::make('footer_message')
                                ->label('Pesan Footer')
                                ->rows(3),
                            Repeater::make('social_links')
                                ->label('Tautan Sosial')
                                ->schema([
                                    TextInput::make('label')
                                        ->label('Nama Platform')
                                        ->required(),
                                    TextInput::make('icon')
                                        ->label('Label Ikon / Emoji')
                                        ->placeholder('IG / FB / YT'),
                                    TextInput::make('url')
                                        ->label('URL')
                                        ->placeholder('https://instagram.com/sekolah')
                                        ->required(),
                                ]),
                        ]),
                ]),
        ]);
    }

    protected function afterSave(): void
    {
        app()->forgetInstance(WebsiteSetting::class);
    }
}
