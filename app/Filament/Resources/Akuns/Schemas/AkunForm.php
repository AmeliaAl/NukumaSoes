<?php

namespace App\Filament\Resources\Akuns\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class AkunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('header_akun')
                ->required()
                ->numeric()
                ->minValue(1)
                ->placeholder('Masukkan header akun'),

            TextInput::make('no_akun')
                ->label('Kode akun')
                ->numeric()
                ->minValue(1)
                ->minLength(3)
                ->placeholder('Masukkan kode akun'),

            TextInput::make('nama_akun')
                ->autocapitalize('words')
                ->label('Nama akun')
                ->required()
                ->placeholder('Masukkan nama akun'),
        ]);
    }
}
