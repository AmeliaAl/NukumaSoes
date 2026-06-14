<?php

namespace App\Filament\Admin\Resources\ReturPenjualans\Schemas;

use App\Models\Barang;
use App\Models\DetailKonsinyasi;
use App\Models\DetailPenjualanNonKonsinyasi;
use App\Models\PenjualanKonsinyasi;
use App\Models\PenjualanNonKonsinyasi;
use App\Models\ReturPenjualan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ReturPenjualanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('no_retur')
                ->label('No Retur')
                ->default(fn () => ReturPenjualan::getNoRetur())
                ->disabled()
                ->dehydrated()
                ->required(),

            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(now())
                ->required(),

            Radio::make('jenis_penjualan')
                ->label('Sumber Penjualan')
                ->options([
                    'non_konsinyasi' => 'Penjualan Non Konsinyasi',
                    'konsinyasi'     => 'Penjualan Konsinyasi',
                ])
                ->default('non_konsinyasi')
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set) {
                    $set('penjualan_non_konsinyasi_id', null);
                    $set('penjualan_konsinyasi_id', null);
                    // tidak reset detailRetur agar baris yang sudah diisi tidak hilang
                })
                ->dehydrated(false),

            Select::make('penjualan_non_konsinyasi_id')
                ->label('No Penjualan')
                ->options(fn () => PenjualanNonKonsinyasi::orderByDesc('id')
                    ->with('pelanggan')
                    ->get()
                    ->mapWithKeys(fn ($p) => [
                        $p->id => $p->no_invoice . ($p->pelanggan ? ' — ' . $p->pelanggan->namaPelanggan : ''),
                    ])
                )
                ->searchable()
                ->live()
                ->visible(fn (Get $get) => $get('jenis_penjualan') !== 'konsinyasi')
                ->required(fn (Get $get) => $get('jenis_penjualan') !== 'konsinyasi')
                ->nullable(),

            Select::make('penjualan_konsinyasi_id')
                ->label('No Penjualan')
                ->options(fn () => PenjualanKonsinyasi::orderByDesc('id')
                    ->with('mitra')
                    ->get()
                    ->mapWithKeys(fn ($p) => [
                        $p->id => $p->no_konsinyasi . ($p->mitra ? ' — ' . $p->mitra->namaMitra : ''),
                    ])
                )
                ->searchable()
                ->live()
                ->visible(fn (Get $get) => $get('jenis_penjualan') === 'konsinyasi')
                ->required(fn (Get $get) => $get('jenis_penjualan') === 'konsinyasi')
                ->nullable(),

            Textarea::make('alasan')
                ->label('Alasan Retur')
                ->rows(2)
                ->required(),

            Repeater::make('detailRetur')
                ->label('Detail Barang Diretur')
                ->relationship('detailRetur')
                ->schema([

                    Select::make('barang_id')
                        ->label('Barang')
                        ->options(function (Get $get) {
                            $jenis = $get('../../jenis_penjualan');
                            $nonKonsinyasiId = $get('../../penjualan_non_konsinyasi_id');
                            $konsinyasiId = $get('../../penjualan_konsinyasi_id');

                            // Non konsinyasi: ambil barang dari detail penjualan
                            if ($jenis !== 'konsinyasi' && $nonKonsinyasiId) {
                                return DetailPenjualanNonKonsinyasi::where('penjualan_id', $nonKonsinyasiId)
                                    ->with('barang.kategori')
                                    ->get()
                                    ->mapWithKeys(fn ($d) => [
                                        $d->barang_id => $d->barang->nama_lengkap,
                                    ]);
                            }

                            // Konsinyasi: ambil barang dari detail konsinyasi
                            if ($jenis === 'konsinyasi' && $konsinyasiId) {
                                return DetailKonsinyasi::where('penjualan_konsinyasi_id', $konsinyasiId)
                                    ->with('barang.kategori')
                                    ->get()
                                    ->mapWithKeys(fn ($d) => [
                                        $d->barang_id => $d->barang->nama_lengkap,
                                    ]);
                            }

                            // Belum pilih penjualan
                            return [];
                        })
                        ->searchable()
                        ->required()
                        ->placeholder(fn (Get $get) =>
                            (! $get('../../penjualan_non_konsinyasi_id') && ! $get('../../penjualan_konsinyasi_id'))
                                ? 'Pilih penjualan terlebih dahulu'
                                : 'Pilih barang'
                        )
                        ->columnSpan(2),

                    TextInput::make('qty')
                        ->label('Qty Diretur')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),

                    Textarea::make('kondisi')
                        ->label('Kondisi Barang')
                        ->placeholder('Contoh: rusak, baik, cacat produksi...')
                        ->rows(1)
                        ->nullable(),

                ])
                ->columns(4)
                ->addActionLabel('+ Tambah Barang')
                ->reorderable(false)
                ->columnSpanFull(),

        ]);
    }
}
