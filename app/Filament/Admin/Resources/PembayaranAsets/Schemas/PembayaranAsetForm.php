<?php

namespace App\Filament\Admin\Resources\PembayaranAsets\Schemas;

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
use Filament\Forms;
use App\Helpers\AkunHelper;

class PembayaranAsetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Pilih Faktur')
                    ->description('Pilih faktur yang akan dibayar')
                    ->schema([
                        Select::make('id_faktur')
                            ->label('No. Faktur')
                            ->relationship('faktur', 'no_faktur', function ($query) {
                                return $query->whereIn('status', ['belum_dibayar', 'belum_lunas'])
                                    ->orderBy('tanggal_faktur', 'desc');
                            })
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "{$record->no_faktur} - {$record->vendor->nama_vendor}"
                            )
                            ->searchable(['no_faktur'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $faktur = \App\Models\FakturPembelian::find($state);
                                    if ($faktur) {
                                        $totalTerbayar = $faktur->pembayaran()->sum('jumlah_bayar');
                                        $sisaTagihan = $faktur->total_tagihan - $totalTerbayar;
                                        $set('jumlah_bayar', $sisaTagihan);
                                    }
                                }
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Info Tagihan')
                    ->description('Ringkasan tagihan faktur')
                    ->schema([
                        Grid::make(4)->schema([
                            Placeholder::make('nama_vendor')
                                ->label('Vendor')
                                ->content(function (Get $get) {
                                    $fakturId = $get('id_faktur');
                                    if (!$fakturId) return '-';
                                    
                                    $faktur = \App\Models\FakturPembelian::find($fakturId);
                                    return $faktur ? $faktur->vendor->nama_vendor : '-';
                                }),

                            Placeholder::make('total_tagihan_display')
                                ->label('Total Tagihan')
                                ->content(function (Get $get) {
                                    $fakturId = $get('id_faktur');
                                    if (!$fakturId) return 'Rp 0';
                                    
                                    $faktur = \App\Models\FakturPembelian::find($fakturId);
                                    return $faktur ? 'Rp ' . number_format($faktur->total_tagihan, 0, ',', '.') : 'Rp 0';
                                })
                                ->extraAttributes(['class' => 'text-lg font-semibold']),

                            Placeholder::make('total_terbayar_display')
                                ->label('Sudah Dibayar')
                                ->content(function (Get $get) {
                                    $fakturId = $get('id_faktur');
                                    if (!$fakturId) return 'Rp 0';
                                    
                                    $faktur = \App\Models\FakturPembelian::find($fakturId);
                                    $totalBayar = $faktur ? $faktur->pembayaran->sum('jumlah_bayar') : 0;
                                    return 'Rp ' . number_format($totalBayar, 0, ',', '.');
                                })
                                ->extraAttributes(['class' => 'text-lg font-semibold text-blue-600']),

                            Placeholder::make('sisa_tagihan_display')
                                ->label('Sisa Tagihan')
                                ->content(function (Get $get) {
                                    $fakturId = $get('id_faktur');
                                    if (!$fakturId) return 'Rp 0';
                                    
                                    $faktur = \App\Models\FakturPembelian::find($fakturId);
                                    if (!$faktur) return 'Rp 0';
                                    
                                    $totalTerbayar = $faktur->pembayaran->sum('jumlah_bayar');
                                    $sisa = $faktur->total_tagihan - $totalTerbayar;
                                    return 'Rp ' . number_format($sisa, 0, ',', '.');
                                })
                                ->extraAttributes(['class' => 'text-lg font-bold text-danger-600']),
                        ]),
                    ]),

                Section::make('Data Pembayaran')
                    ->description('Masukkan informasi pembayaran')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('tanggal_bayar')
                                ->label('Tanggal Bayar')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now())
                                ->rule(function (Get $get) {
                                    return function ($attribute, $value, $fail) use ($get) {
                                        $fakturId = $get('id_faktur');
                                        if (!$fakturId) return;

                                        $faktur = \App\Models\FakturPembelian::find($fakturId);

                                        if ($faktur && $value < $faktur->tanggal_faktur) {
                                            $fail('Tanggal bayar tidak boleh sebelum tanggal faktur.');
                                        }
                                    };
                                })
                                ->columnSpan(1),

                            DatePicker::make('tgl_terima_brg')
                                ->label('Tanggal Terima Barang')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->helperText('Tanggal saat barang diterima')
                                ->rule(function (Get $get) {
                                    return function ($attribute, $value, $fail) use ($get) {
                                        $fakturId = $get('id_faktur');
                                        if (!$fakturId || !$value) return;

                                        $faktur = \App\Models\FakturPembelian::find($fakturId);

                                        if ($faktur && $value < $faktur->tanggal_faktur) {
                                            $fail('Tanggal terima tidak boleh sebelum tanggal faktur.');
                                        }

                                        if ($value < $get('tanggal_bayar')) {
                                            $fail('Tanggal terima tidak boleh sebelum tanggal bayar.');
                                        }
                                    };
                                })
                                ->columnSpan(1),

                            Select::make('metode_pembayaran')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'cash' => 'Cash',
                                    'transfer' => 'Transfer Bank',
                                ])
                                ->default('cash')
                                ->required()
                                ->native(false)
                                ->columnSpan(1),

                           Select::make('id_akun')
                                ->label('Bayar Dari Akun')
                                ->relationship(
                                    'akun',
                                    'nama_akun',
                                    fn ($query) => $query->where('nama_akun', 'like', 'kas%')
                                )
                                

                                ->getOptionLabelFromRecordUsing(fn ($record) =>
                                    "{$record->nama_akun} ({$record->no_akun}) - Rp " .
                                    number_format(\App\Helpers\AkunHelper::getSaldo($record->id), 0, ',', '.')
                                )
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {

                                    $akunId = $get('id_akun');
                                    $tanggal = $get('tanggal');

                                    if (!$akunId || !$tanggal) return;

                                    $saldo = \App\Helpers\AkunHelper::getSaldo($akunId, $tanggal);

                                    if ($state > $saldo) {
                                        $set('jumlah_bayar', $saldo);
                                    }
                                })
                                ->rules([
                                    function (Get $get) {
                                        return function ($attribute, $value, $fail) use ($get) {

                                            $akunId  = $get('id_akun');
                                            $tanggal = $get('tanggal');

                                            if (!$akunId || !$tanggal) return;

                                            $saldo = \App\Helpers\AkunHelper::getSaldo($akunId, $tanggal);

                                            if ($value > $saldo) {
                                                $fail('Saldo akun tidak cukup untuk pembayaran ini. Sisa: Rp ' . number_format($saldo, 0, ',', '.'));
                                            }
                                        };
                                    }
])
                                ->searchable()
                                ->preload()
                                ->required()
                                ->visible(fn (Get $get) => $get('metode_pembayaran') === 'transfer'),

                           

                            TextInput::make('jumlah_bayar')
                                ->label('Jumlah Bayar')
                                ->required()
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(1)
                                ->helperText('Masukkan jumlah yang akan dibayarkan')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                    $fakturId = $get('id_faktur');
                                    if ($fakturId && $state) {
                                        $faktur = \App\Models\FakturPembelian::find($fakturId);
                                        if ($faktur) {
                                            $totalTerbayar = $faktur->pembayaran->sum('jumlah_bayar');
                                            $sisaTagihan = $faktur->total_tagihan - $totalTerbayar;
                                            
                                            if ($state > $sisaTagihan) {
                                                $set('jumlah_bayar', $sisaTagihan);
                                            }
                                        }
                                    }
                                     $akunId = $get('id_akun');
                                        if ($akunId) {
                                            $akun = \App\Models\Akun::find($akunId);

                                            $saldo = \App\Helpers\AkunHelper::getSaldo($akunId);

                                            if ($state > $saldo) {
                                                $set('jumlah_bayar', $saldo);
                                            }
                                        }
                                })
                                ->columnSpan(1)
                                ->live(onBlur: true)
                                ->rule(function (Get $get) {
                                    return function ($attribute, $value, $fail) use ($get) {

                                        $akunId = $get('id_akun');
                                        if (!$akunId) return;

                                        $saldo = \App\Helpers\AkunHelper::getSaldo($akunId);

                                        if ($value > $saldo) {
                                            $fail('Saldo akun tidak cukup untuk pembayaran ini.');
                                        }
                                    };
                                }),

                            
                        ]),
                    ]),

                Section::make('Preview Status')
                    ->description('Estimasi status faktur setelah pembayaran ini')
                    ->schema([
                        Placeholder::make('status_preview')
                            ->label('Status Faktur Akan Menjadi')
                            ->content(function (Get $get) {
                                $fakturId = $get('id_faktur');
                                $jumlahBayar = (float) ($get('jumlah_bayar') ?? 0);
                                
                                if (!$fakturId || !$jumlahBayar) return '⚪ -';
                                
                                $faktur = \App\Models\FakturPembelian::find($fakturId);
                                if (!$faktur) return '⚪ -';
                                
                                $totalTerbayar = $faktur->pembayaran->sum('jumlah_bayar');
                                $totalSetelahBayar = $totalTerbayar + $jumlahBayar;
                                
                                if ($totalSetelahBayar >= $faktur->total_tagihan) {
                                    return '🟢 LUNAS';
                                } elseif ($totalSetelahBayar > 0) {
                                    return '🟡 BELUM LUNAS (Sisa: Rp ' . number_format($faktur->total_tagihan - $totalSetelahBayar, 0, ',', '.') . ')';
                                } else {
                                    return '🔴 BELUM DIBAYAR';
                                }
                            })
                            ->extraAttributes(['class' => 'text-xl font-bold']),
                    ]),
            ]);
    }
}