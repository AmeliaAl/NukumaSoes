<?php

namespace App\Filament\Admin\Resources\Kategoris\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KategoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Kategori')
                    ->description('Isi data kategori kemasan barang terlebih dahulu sebelum menambahkan barang.')
                    ->schema([
                        TextInput::make('nama_kategori')
                            ->label('Nama Kategori')
                            ->required()
                            ->placeholder('Contoh: Soes Kemasan Plastik 55 gr')
                            ->columnSpanFull(),

                        TextInput::make('berat')
                            ->label('Berat (gr)')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('gr')
                            ->placeholder('Contoh: 55'),

                        TextInput::make('jenis_kemasan')
                            ->label('Jenis Kemasan')
                            ->required()
                            ->placeholder('Contoh: Plastik'),

                        TextInput::make('masa_simpan')
                            ->label('Masa Simpan')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Contoh: 10'),

                        TextInput::make('unit_waktu')
                            ->label('Unit Waktu')
                            ->required()
                            ->placeholder('Contoh: Bulan'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}