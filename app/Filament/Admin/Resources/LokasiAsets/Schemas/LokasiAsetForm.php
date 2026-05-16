<?php

namespace App\Filament\Admin\Resources\LokasiAsets\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\LokasiAset;

class LokasiAsetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_lokasi')
                ->label('Kode Lokasi')
                ->default(fn () => lokasiAset::generateKdLokasi()) // Ambil default dari method generatekdaset
                ->readonly()
                ->unique(ignoreRecord: true)
                ->maxLength(20),

                TextInput::make('nama_lokasi')
                ->label('Nama Lokasi')
                ->required()
                ->maxLength(100),
            ]);
    }
}
