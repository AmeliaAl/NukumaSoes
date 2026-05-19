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
<<<<<<< HEAD
                ->numeric()
                ->minValue(1)
=======
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
                ->placeholder('Masukkan header akun'),

            TextInput::make('no_akun')
                ->label('Kode akun')
<<<<<<< HEAD
                ->numeric()
                ->minValue(1)
                ->minLength(3)
=======
                ->required()
>>>>>>> 0e9a011746a93c000fadadc9232429c9cae71eb2
                ->placeholder('Masukkan kode akun'),

            TextInput::make('nama_akun')
                ->autocapitalize('words')
                ->label('Nama akun')
                ->required()
                ->placeholder('Masukkan nama akun'),
        ]);
    }
}
