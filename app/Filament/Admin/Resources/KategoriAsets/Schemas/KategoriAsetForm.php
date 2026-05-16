<?php

namespace App\Filament\Admin\Resources\KategoriAsets\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class KategoriAsetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            TextInput::make('kode_kategori')
                ->required()
                ->label('Kode Kategori')
                ->required()
                ->placeholder('Masukkan kode kategori'),

            TextInput::make('nama_kategori')
                ->required()
                ->label('Nama Kategori')
                ->required()
                ->placeholder('Masukkan nama kategori'),

            Select::make('jenis_aset')
                ->label('Jenis Aset')
                ->options([
                    'aset_tetap' => 'Aset Tetap',
                    'aset_lancar' => 'Aset Lancar',
                ])
                ->required(),

            TextInput::make('metode_penyusutan')
                ->autocapitalize('words')
                ->label('Metode Penyusutan')
                ->required()
                -> readonly()
                ->default('Garis Lurus'),

            TextInput::make('masa_manfaat')
                ->label('Masa Manfaat (dalam tahun)')
                ->required()
                ->numeric()
                ->placeholder('Masukkan masa manfaat dalam tahun'),

            TextInput::make('interval_pemeliharaan')
                ->label('Interval Pemeliharaan (dalam bulan)')
                ->required()
                ->numeric()
                ->suffix('bulan')
                ->placeholder('Masukkan interval pemeliharaan dalam bulan'),    
            ]);
    }
}
