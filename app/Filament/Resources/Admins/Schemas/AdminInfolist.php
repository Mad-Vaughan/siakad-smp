<?php

namespace App\Filament\Resources\Admins\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AdminInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nama Lengkap'),

                TextEntry::make('email')
                    ->label('Alamat Email'),

                TextEntry::make('email_verified_at')
                    ->label('Email Diverifikasi Pada')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('nisn')
                    ->label('NISN')
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
                    ->label('Diperbarui Pada') // 👈 Udah diganti ke Indo
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
