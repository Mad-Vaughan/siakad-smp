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
                    ->label('Nama Lengkap'), // 👈 Udah diganti ke Indo

                TextEntry::make('email')
                    ->label('Alamat Email'), // 👈 Udah diganti ke Indo

                TextEntry::make('email_verified_at')
                    ->label('Email Diverifikasi Pada') // 👈 Udah diganti ke Indo
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('nisn')
                    ->label('NISN') // 👈 Udah diganti ke Indo (Biar kapitalnya rapi)
                    ->placeholder('-'),

                TextEntry::make('date_of_birth')
                    ->label('Tanggal Lahir') // 👈 Udah diganti ke Indo
                    ->date()
                    ->placeholder('-'),

                TextEntry::make('gender')
                    ->label('Jenis Kelamin') // 👈 Udah diganti ke Indo
                    ->badge()
                    ->placeholder('-'),

                TextEntry::make('address')
                    ->label('Alamat Lengkap') // 👈 Udah diganti ke Indo
                    ->placeholder('-'),

                TextEntry::make('created_at')
                    ->label('Dibuat Pada') // 👈 Udah diganti ke Indo
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Diperbarui Pada') // 👈 Udah diganti ke Indo
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}