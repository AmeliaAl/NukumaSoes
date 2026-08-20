<?php

namespace App\Filament\Resources\CoaResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CoaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('header_akun')
                    ->required()
                    ->numeric(),
                TextInput::make('kode_akun')
                    ->required()
                    ->numeric(),
                TextInput::make('nama_akun')
                    ->required()
                    ->rule('regex:/^[a-zA-Z\s]+$/')
                    ->validationMessages([
                        'regex' => 'Nama akun hanya boleh berisi huruf.',
                    ]),
            ]);
    }
}
