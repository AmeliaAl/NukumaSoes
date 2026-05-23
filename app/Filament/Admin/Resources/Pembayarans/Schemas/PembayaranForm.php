<?php

namespace App\Filament\Admin\Resources\Pembayarans\Schemas;

use App\Models\PenjualanNonKonsinyasi;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('kode_pembayaran')
                ->label('Kode Pembayaran')
                ->default(fn () => \App\Models\Pembayaran::getKodePembayaran())
                ->disabled()
                ->dehydrated()
                ->required(),

            Select::make('penjualan_id')
                ->label('No Invoice')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->options(
                    PenjualanNonKonsinyasi::query()
                        ->whereRaw('
                            total > (
                                SELECT IFNULL(SUM(jumlah_bayar), 0)
                                FROM pembayaran
                                WHERE pembayaran.penjualan_id = penjualan_non_konsinyasi.id
                            )
                        ')
                        ->pluck('no_invoice', 'id')
                )
                ->afterStateUpdated(function ($state, callable $set) {
                    $penjualan = PenjualanNonKonsinyasi::find($state);

                    if ($penjualan) {
                        $totalBayar = $penjualan->pembayaran()->sum('jumlah_bayar');
                        $sisa       = max(($penjualan->total ?? 0) - $totalBayar, 0);

                        $set('pelanggan_id', $penjualan->pelanggan_id);
                        $set('total_transaksi', $penjualan->total);
                        $set('jumlah_bayar', $sisa);
                        // Set tanggal bayar default ke tanggal penjualan jika belum diisi
                        $set('tanggal_bayar', now()->toDateString());
                    } else {
                        $set('pelanggan_id', null);
                        $set('total_transaksi', 0);
                        $set('jumlah_bayar', 0);
                    }
                }),

            // Tanggal bayar — minimal = tanggal penjualan yang dipilih
            DatePicker::make('tanggal_bayar')
                ->label('Tanggal Bayar')
                ->default(now())
                ->required()
                ->minDate(function ($get) {
                    $penjualan = PenjualanNonKonsinyasi::find($get('penjualan_id'));
                    return $penjualan?->tanggal;
                })
                ->helperText(function ($get) {
                    $penjualan = PenjualanNonKonsinyasi::find($get('penjualan_id'));
                    if (! $penjualan?->tanggal) return null;
                    return 'Minimal: ' . \Carbon\Carbon::parse($penjualan->tanggal)->translatedFormat('d M Y');
                }),

            // Pelanggan (auto dari invoice)
            Select::make('pelanggan_id')
                ->label('Pelanggan')
                ->relationship('pelanggan', 'namaPelanggan')
                ->disabled()
                ->dehydrated()
                ->required(),

            // Total transaksi (read only)
            TextInput::make('total_transaksi')
                ->label('Total')
                ->numeric()
                ->disabled()
                ->dehydrated(false)
                ->prefix('Rp'),

            // Jumlah bayar — otomatis terisi = sisa, tapi bisa diedit
            TextInput::make('jumlah_bayar')
                ->label('Jumlah Bayar')
                ->numeric()
                ->required()
                ->minValue(1)
                ->rules([
                    fn (callable $get) => function (string $attribute, $value, $fail) use ($get) {
                        $penjualan = PenjualanNonKonsinyasi::find($get('penjualan_id'));
                        if (! $penjualan) return;

                        $totalBayar = $penjualan->pembayaran()->sum('jumlah_bayar');
                        $sisa       = max(($penjualan->total ?? 0) - $totalBayar, 0);

                        if ($value > $sisa) {
                            $fail('Jumlah bayar tidak boleh melebihi total (Rp ' . number_format($sisa, 0, ',', '.') . ').');
                        }
                    },
                ]),

            Select::make('metode_pembayaran')
                ->label('Metode Pembayaran')
                ->options([
                    'tunai'    => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris'     => 'QRIS',
                ])
                ->required(),

            FileUpload::make('bukti_bayar')
                ->label('Bukti Bayar')
                ->image()
                ->directory('bukti-bayar')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->placeholder('Posting bukti pembayaran atau <span class="filepond--label-action">Jelajahi</span>')
                ->helperText('Format: JPG, PNG, WEBP. Maks 2MB.')
                ->nullable(),

            Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(3),
        ]);
    }
}
