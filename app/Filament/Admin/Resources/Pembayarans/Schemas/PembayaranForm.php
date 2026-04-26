<?php

namespace App\Filament\Admin\Resources\Pembayarans\Schemas;

use App\Models\PenjualanNonKonsinyasi;
use Filament\Forms\Components\DatePicker;
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

            DatePicker::make('tanggal_bayar')
                ->label('Tanggal Bayar')
                ->default(now())
                ->required(),

            /* ===============================
             * INVOICE (BELUM LUNAS)
             * =============================== */
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
                        $set('pelanggan_id', $penjualan->pelanggan_id);
                        $totalBayar = $penjualan->pembayaran()->sum('jumlah_bayar');
                        $set(
                            'sisa_piutang',
                            $penjualan->total - $totalBayar
                        );
                    } else {
                        $set('pelanggan_id', null);
                        $set('sisa_piutang', 0);
                    }
                }),

            /* ===============================
             * PELANGGAN (AUTO)
             * =============================== */
            Select::make('pelanggan_id')
                ->label('Pelanggan')
                ->relationship('pelanggan', 'namaPelanggan')
                ->disabled()
                ->dehydrated()
                ->required(),

            /* ===============================
             * SISA PIUTANG (READ ONLY)
             * =============================== */
            TextInput::make('sisa_piutang')
                ->label('Sisa Piutang')
                ->numeric()
                ->disabled()
                ->dehydrated(false),

            TextInput::make('jumlah_bayar')
                ->label('Jumlah Bayar')
                ->numeric()
                ->required()
                ->minValue(1)
                ->rules([
                    fn (callable $get) => function (string $attribute, $value, $fail) use ($get) {
                        $sisa = $get('sisa_piutang');

                        if ($value > $sisa) {
                            $fail('Jumlah bayar tidak boleh melebihi sisa piutang.');
                        }
                    },
                ])
                ->helperText('Tidak boleh lebih besar dari sisa piutang'),

            Select::make('metode_pembayaran')
                ->label('Metode Pembayaran')
                ->options([
                    'tunai' => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris' => 'QRIS',
                ])
                ->required(),

            Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(3),
        ]);
    }
}
