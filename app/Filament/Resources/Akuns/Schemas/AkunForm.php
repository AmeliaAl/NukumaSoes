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
                ->placeholder('Masukkan header akun'),

            TextInput::make('no_akun')
                ->label('Kode akun')
                ->required()
                ->placeholder('Masukkan kode akun'),

            TextInput::make('nama_akun')
                ->autocapitalize('words')
                ->label('Nama akun')
                ->required()
                ->placeholder('Masukkan nama akun'),
        ]);
    }
}
