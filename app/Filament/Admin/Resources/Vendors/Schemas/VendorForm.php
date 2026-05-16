<?php

namespace App\Filament\Admin\Resources\Vendors\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_vendor')
                ->label('Nama Vendor')
                ->required()
                ->maxLength(50),

                TextInput::make('kontak_vendor')
                ->label('Kontak Vendor')
                ->required(),

                Textarea::make('alamat_vendor')
                ->label('Alamat Vendor')
                ->required()
                ->rows(3)
                ->columnSpanFull(),

            ]);
    }
}
