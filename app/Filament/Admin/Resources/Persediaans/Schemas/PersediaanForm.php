<?php

namespace App\Filament\Admin\Resources\Persediaans\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\FakturPembelian;
use App\Models\FakturPembelianItem;
use Carbon\Carbon;

class PersediaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Pilih dari Faktur')
                    ->description('Pilih faktur pembelian dan item barang')
                    ->schema([
                        Grid::make(2)->schema([
                            // 1️⃣ PILIH FAKTUR
                            Select::make('id_faktur')
                                ->label('Faktur Pembelian')
                                ->options(
                                    FakturPembelian::query()
                                        ->with('vendor')
                                        ->get()
                                        ->mapWithKeys(function ($faktur) {
                                            $tanggal = $faktur->tanggal_faktur instanceof Carbon 
                                                ? $faktur->tanggal_faktur->format('d/m/Y')
                                                : Carbon::parse($faktur->tanggal_faktur)->format('d/m/Y');
                                            
                                            return [
                                                $faktur->id => $faktur->no_faktur . 
                                                    ' - ' . ($faktur->vendor->nama_vendor ?? 'Unknown') . 
                                                    ' (' . $tanggal . ')'
                                            ];
                                        })
                                        ->toArray()
                                )
                                ->searchable()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    // Reset semua field
                                    $set('id_faktur_item', null);
                                    $set('id_vendor', null);
                                    $set('nama_barang', null);
                                    $set('id_kategori', null);
                                    $set('qty', null);
                                    $set('harga_satuan', null);
                                    $set('total', null);
                                    
                                    // ✅ SET ID_VENDOR DARI FAKTUR
                                    if ($state) {
                                        $faktur = FakturPembelian::find($state);
                                        if ($faktur) {
                                            $set('id_vendor', $faktur->id_vendor);
                                        }
                                    }
                                })
                                ->columnSpan(2),

                            // 2️⃣ PILIH ITEM DARI FAKTUR
                            Select::make('id_faktur_item')
                                ->label('Item Faktur')
                                ->options(function (Get $get) {
                                    $fakturId = $get('id_faktur');
                                    
                                    if (!$fakturId) {
                                        return [];
                                    }

                                    return FakturPembelianItem::query()
                                        ->with('kategoriAset')
                                        ->where('id_faktur', $fakturId)
                                            ->whereHas('kategoriAset', function ($query) {
                                                $query->where('jenis_aset', 'aset_lancar'); 
                                            })
                                        ->get()
                                        ->mapWithKeys(function ($item) {
                                            return [
                                                $item->id => $item->nama_aset . 
                                                    ' (' . $item->kategoriAset->nama_kategori . ')' .
                                                    ' - Qty: ' . $item->qty . 
                                                    ' @ Rp ' . number_format($item->harga_satuan, 0, ',', '.')
                                            ];
                                        })
                                        ->toArray();
                                })
                                ->searchable()
                                ->required()
                                ->live()
                                ->disabled(fn (Get $get) => blank($get('id_faktur')))
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (!$state) {
                                        return;
                                    }

                                    $item = FakturPembelianItem::with(['kategoriAset', 'faktur'])->find($state);
                                    
                                    if (!$item) {
                                        return;
                                    }

                                    $set('nama_barang', $item->nama_aset);
                                    $set('id_kategori', $item->id_kategori);
                                    $set('satuan', $item->keterangan ?? 'Unit'); // Ambil dari keterangan
                                    $set('qty', $item->qty);
                                    $set('harga_satuan', $item->harga_satuan);
                                    $set('total', $item->total_harga);
                                    $set('tanggal_masuk', now());
                                })
                                ->helperText('Pilih item dari faktur yang dipilih')
                                ->columnSpan(2),
                        ]),
                    ])
                    ->columnSpan(2),

                Section::make('Informasi Vendor')
                    ->description('Data vendor dari faktur pembelian')
                    ->schema([
                        Select::make('id_vendor')
                            ->label('Vendor')
                            ->relationship('vendor', 'nama_vendor')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->columnSpan(2),

                Section::make('Detail Persediaan')
                    ->description('Informasi barang yang masuk ke persediaan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('nama_barang')
                                ->label('Nama Barang')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('satuan')
                                ->label('Satuan')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->default('Unit')
                                ->columnSpan(1),

                            Select::make('id_kategori')
                                ->label('Kategori')
                                ->relationship('kategoriAset', 'nama_kategori')
                                
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),

                            DatePicker::make('tanggal_masuk')
                                ->label('Tanggal Masuk')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->columnSpan(1),

                            TextInput::make('qty')
                                ->label('Quantity')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('harga_satuan')
                                ->label('Harga Satuan')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),

                            TextInput::make('total')
                                ->label('Total Harga')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(2),
                        ]),
                    ])
                    ->columnSpan(2),
            ]);
    }
}