<?php

namespace App\Filament\Admin\Resources\HargaBarangs\Schemas;

use App\Models\Barang;
use App\Models\Kategori;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class HargaBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('kategori_filter')
                ->label('Kategori')
                ->options(fn () => Kategori::orderBy('nama_kategori')->pluck('nama_kategori', 'id'))
                ->searchable()
                ->live()
                ->dehydrated(false)
                ->placeholder('Semua Kategori'),

            Select::make('barang_id')
                ->label('Nama Barang')
                ->options(function (Get $get) {
                    $query = Barang::with('kategori')->orderBy('nama_barang');

                    if ($get('kategori_filter')) {
                        $query->where('kategori_id', $get('kategori_filter'));
                    }

                    return $query->get()->mapWithKeys(fn ($b) => [$b->id => $b->nama_barang]);
                })
                ->searchable()
                ->required()
                ->live(),

            Select::make('jenis_mitra')
                ->label('Jenis Mitra')
                ->options([
                    'agen'       => 'Agen',
                    'reseller'   => 'Reseller',
                    'konsinyasi' => 'Konsinyasi',
                    'umum'       => 'Umum',
                ])
                ->required(),

            TextInput::make('harga')
                ->label('Harga')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->required(),

        ]);
    }
}
