<?php

namespace App\Filament\Admin\Resources\Barangs\Schemas;

use App\Models\Barang;
use App\Models\Kategori;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Barang')
                    ->schema([
                        TextInput::make('kode_barang')
                            ->default(fn () => Barang::getKodeBarang())
                            ->label('Kode Barang')
                            ->required(),

                        TextInput::make('nama_barang')
                            ->label('Nama Barang')
                            ->required()
                            ->placeholder('Masukkan nama barang'),

                        TextInput::make('rasa')
                            ->label('Rasa')
                            ->required()
                            ->placeholder('Masukkan rasa barang'),

                        Select::make('kategori_id')
                            ->label('Kategori')
                            ->options(fn () => Kategori::query()->pluck('nama_kategori', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('satuan')
                            ->label('Satuan')
                            ->default('PCS')
                            ->required(),

                        TextInput::make('stok_awal')
                            ->label('Stok Awal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Harga per Jenis Mitra')
                    ->schema([
                        Repeater::make('hargaBarang')
                            ->relationship('hargaBarang')
                            ->label('Harga Barang')
                            ->schema([
                                Select::make('jenis_mitra')
                                    ->label('Tipe Harga')
                                    ->options([
                                        'agen' => 'Agen',
                                        'reseller' => 'Reseller',
                                        'konsinyasi' => 'Konsinyasi',
                                        'umum' => 'Umum',
                                    ])
                                    ->required(),

                                TextInput::make('harga')
                                    ->label('Harga')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Harga'),
                    ]),
            ]);
    }
}