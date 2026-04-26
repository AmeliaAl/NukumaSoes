<?php

namespace App\Filament\Admin\Resources\Pelanggans\Schemas;

use App\Models\Pelanggan;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_pelanggan')
                    ->default(fn () => Pelanggan::getKodePelanggan()) // Ambil default dari method getKodeMitra
                    ->label('Kode Pelanggan')
                    ->required(),
                TextInput::make('namaPelanggan')
                    ->placeholder('Masukkan nama Pelanggan')
                    ->required(),
                TextInput::make('alamat')
                    ->placeholder('Masukkan Alamat Pelanggan')
                    ->required(),
                TextInput::make('no_telepon')
                    ->placeholder('No Telepon')
                    ->tel()
                    ->numeric()
                    ->maxLength(12)
                    ->regex('/^[0-9]+$/')
                    ->required(),
            ]);
    }
}
