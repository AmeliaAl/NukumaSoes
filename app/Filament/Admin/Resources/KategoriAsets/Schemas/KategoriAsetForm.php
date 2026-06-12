<?php

namespace App\Filament\Admin\Resources\KategoriAsets\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;

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
                ->live()
                ->required(),

            TextInput::make('metode_penyusutan')
                ->autocapitalize('words')
                ->label('Metode Penyusutan')
                ->required()
                -> readonly()
                ->default('Garis Lurus')
                ->visible(fn (Get $get) => $get('jenis_aset') === 'aset_tetap'),

            TextInput::make('masa_manfaat')
                ->label('Masa Manfaat (tahun)')
                ->required()
                ->numeric()
                ->minValue(1)
                ->rule('digits_between:1,2')
                ->validationMessages([
                    'digits_between' => 'Maksimal hanya 2 digit angka.',
                ])
                ->placeholder('Masukkan masa manfaat dalam tahun')
                ->visible(fn (Get $get) => $get('jenis_aset') === 'aset_tetap'),

            TextInput::make('interval_pemeliharaan')
                ->label('Interval Pemeliharaan (bulan)')
                ->required()
                ->numeric()
                ->minValue(0)
                ->rule('digits_between:1,2')
                ->validationMessages([
                    'digits_between' => 'Maksimal hanya 2 digit angka.',
                ])
                ->suffix('bulan')
                ->placeholder('Masukkan interval pemeliharaan dalam bulan')
                ->visible(fn (Get $get) => $get('jenis_aset') === 'aset_tetap'),   
            ]);
    }
}
