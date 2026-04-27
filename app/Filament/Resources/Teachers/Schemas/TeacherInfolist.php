<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap'), // 👈 Mantap kearifan lokal
                        
                        TextEntry::make('email')
                            ->label('Alamat Email'),
                            
                        TextEntry::make('email_verified_at')
                            ->label('Email Diverifikasi Pada')
                            ->dateTime()
                            ->placeholder('-'),
                            
                        TextEntry::make('nisn')
                            ->label('NIP / NISN') // 👈 Disesuaikan buat guru
                            ->placeholder('-'),
                            
                        TextEntry::make('date_of_birth')
                            ->label('Tanggal Lahir')
                            ->date()
                            ->placeholder('-'),
                            
                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->badge()
                            ->placeholder('-'),
                            
                        TextEntry::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('-'),
                            
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime()
                            ->placeholder('-'),
                            
                        TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}