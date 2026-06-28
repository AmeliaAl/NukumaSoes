<?php

namespace App\Filament\Admin\Resources\PerolehanAsets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use App\Models\FakturPembelian;
use App\Models\FakturPembelianItem;


class PerolehanAsetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Pilih dari Faktur Pembelian')
                    ->description('Pilih faktur dan item yang sudah dibeli')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('id_faktur')
                                    ->label('Faktur Pembelian')
                                    ->options(
                                        FakturPembelian::whereHas('items', function ($query) {
                                            $query->whereHas('kategoriAset', function ($q) {
                                                $q->where('jenis_aset', 'aset_tetap');
                                            })
                                            ->whereDoesntHave('perolehanAset');
                                        })
                                        ->pluck('no_faktur', 'id')
                                        ->toArray()
                                    )
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $set('id_faktur_item', null);
                                        
                                        if ($state) {
                                            $faktur = FakturPembelian::find($state);
                                            if ($faktur) {
                                                $set('id_vendor', $faktur->id_vendor);
                                                $set('no_faktur', $faktur->no_faktur);
                                                $set('tanggal_faktur', $faktur->tanggal_faktur);
                                            }
                                        }
                                    })
                                    ->columnSpan(2),

                                Select::make('id_faktur_item')
                                    ->label('Item yang Dibeli')
                                    ->options(function (Get $get) {
                                        $fakturId = $get('id_faktur');
                                        if (!$fakturId) {
                                            return [];
                                        }

                                        return FakturPembelianItem::with('kategoriAset')
                                            ->where('id_faktur', $fakturId)

                                            // ❗ FILTER: cuma ambil yg BELUM pernah dipakai
                                            ->whereDoesntHave('perolehanAset')

                                            ->whereHas('kategoriAset', function ($query) {
                                                $query->where('jenis_aset', 'aset_tetap'); 
                                            })
                                            ->get()
                                            ->mapWithKeys(function ($item) {
                                                return [
                                                    $item->id => $item->nama_aset . 
                                                        ' (' . $item->kategoriAset->nama_kategori . ')' .
                                                        ' - Qty: ' . $item->qty . 
                                                        ' @ Rp ' . number_format($item->harga_satuan, 0, ',', '.') .
                                                        ' = Rp ' . number_format($item->total_harga, 0, ',', '.')
                                                ];
                                            });
                                    })
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if (!$state) {
                                            return;
                                        }
                                        
                                        $item = FakturPembelianItem::with(['kategoriAset', 'faktur'])->find($state);
                                        if ($item) {
                                            $set('nama_aset', $item->nama_aset);
                                            $set('id_kategori', $item->id_kategori);
                                            $set('qty', $item->qty);
                                            $set('harga_satuan', $item->harga_satuan);
                                            
                                            $alokasiBiayaLain = $item->faktur->getAlokasiBiayaLain();
                                            $biayaLain = $alokasiBiayaLain[$item->id] ?? 0;
                                            $set('biaya_lain', $biayaLain);
                                            
                                            $totalPerolehan = $item->total_harga + $biayaLain;
                                            $set('total_perolehan', $totalPerolehan);
                                            
                                            if (isset($item->nilai_residu)) {
                                                $set('nilai_residu', $item->nilai_residu);
                                            }
                                            
                                            if ($item->kategoriAset && $item->kategoriAset->masa_manfaat) {
                                                $set('masa_manfaat', $item->kategoriAset->masa_manfaat);
                                            } else {
                                                $set('masa_manfaat', 0);
                                            }
                                        }
                                    })
                                    ->disabled(fn (Get $get) => !$get('id_faktur'))
                                    ->helperText('Pilih item aset yang akan dicatat sebagai perolehan')
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->columnSpan(2),

                Section::make('Informasi Faktur')
                    ->description('Data dari faktur pembelian (Tidak dapat diedit)')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('id_vendor')
                                    ->label('Vendor')
                                    ->relationship('vendor', 'nama_vendor')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('no_faktur')
                                    ->label('No. Faktur')
                                    ->disabled()
                                    ->dehydrated()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                DatePicker::make('tanggal_faktur')
                                    ->label('Tanggal Faktur')
                                    ->disabled()
                                    ->dehydrated()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpan(2),

                Section::make('Detail Aset')
                    ->description('Informasi aset dari faktur (Tidak dapat diedit)')
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nama_aset')
                                    ->label('Nama Aset')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(2),

                                select::make('kode_lokasi')
                                    ->label('Lokasi Aset')
                                    ->relationship('lokasiAset', 'nama_lokasi')
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(1),

                                DatePicker::make('tanggal_pakai')
                                    ->label('Tanggal Mulai Pakai')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->rule(function (Get $get) {
                                        return function ($attribute, $value, $fail) use ($get) {

                                            $fakturId = $get('id_faktur');
                                            if (!$fakturId || !$value) return;

                                            $faktur = \App\Models\FakturPembelian::find($fakturId);

                                            if ($faktur) {
                                                // 🔥 ambil pembayaran terakhir
                                                $pembayaran = $faktur->pembayaran()->latest()->first();

                                                if ($pembayaran && $pembayaran->tgl_terima_brg) {
                                                    
                                                    if ($value < $pembayaran->tgl_terima_brg) {
                                                        $fail('Tanggal mulai pakai tidak boleh sebelum tanggal barang diterima.');
                                                    }

                                                }
                                            }
                                        };
                                    })
                                    ->live(onBlur: true)
                                    ->columnSpan(1),

                                select::make('id_kategori')
                                    ->label('Kategori Aset')
                                    ->relationship('kategoriAset', 'nama_kategori')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(2),

                                TextInput::make('qty')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->suffix('unit')
                                    ->columnSpan(1),

                                TextInput::make('harga_satuan')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('biaya_lain')
                                    ->label('Biaya Lain-lain')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Dari faktur pembelian')
                                    ->columnSpan(1),

                                Placeholder::make('total_perolehan_display')
                                    ->label('Total Perolehan')
                                    ->content(function (Get $get): string {
                                        $total = (float) ($get('total_perolehan') ?? 0);
                                        return 'Rp ' . number_format($total, 0, ',', '.');
                                    })
                                    ->columnSpan(1),

                                TextInput::make('total_perolehan')
                                    ->numeric()
                                    ->hidden()
                                    ->dehydrated()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(2),

                Section::make('Informasi Penyusutan')
                    ->description('Data untuk perhitungan depresiasi')
                    ->icon('heroicon-o-chart-bar-square')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('masa_manfaat')
                                    ->label('Masa Manfaat')
                                    ->required()
                                    ->minValue(1)
                                    ->numeric()
                                    ->suffix('tahun')
                                    ->helperText('Estimasi umur ekonomis aset')
                                    ->columnSpan(1),

                                TextInput::make('nilai_residu')
                                    ->label('Nilai Residu')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->rule(function (Get $get) {
                                        return function ($attribute, $value, $fail) use ($get) {
                                            $totalPerolehan = (float) ($get('total_perolehan') ?? 0);
                                            if ((float) $value >= $totalPerolehan) {
                                                $fail('Nilai residu harus lebih kecil dari total perolehan.');
                                            }
                                        };
                                    })
                                    ->validationMessages([])
                                    ->prefix('Rp'),

                                TextInput::make('metode_penyusutan')
                                    ->label('Metode Penyusutan')
                                    ->readonly()
                                    ->default('Garis Lurus')
                                    ->columnSpan(2),

                              
                            ]),
                    ])
                    ->columnSpan(2),
            ]);
    }
}