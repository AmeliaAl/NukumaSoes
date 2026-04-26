<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\Schemas;

use App\Models\PenjualanNonKonsinyasi;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;

class PenjualanNonKonsinyasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('no_invoice')
                ->label('No Invoice')
                ->default(fn () => PenjualanNonKonsinyasi::getNoInvoice())
                ->disabled()
                ->dehydrated()
                ->required(),

            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(now())
                ->required()
                ->disabled(fn ($record) => $record?->isLocked()),

            Select::make('pelanggan_id')
                ->label('Pelanggan')
                ->relationship('pelanggan', 'namaPelanggan')
                ->preload()
                ->searchable()
                ->required()
                ->disabled(fn ($record) => $record?->status === 'LUNAS'),

            Radio::make('jenis_pembayaran')
                ->label('Jenis Pembayaran')
                ->options([
                    'tunai' => 'Tunai',
                    'kredit' => 'Piutang',
                ])
                ->default('tunai')
                ->required()
                ->live(),

            DatePicker::make('jatuh_tempo')
                ->label('Jatuh Tempo')
                ->visible(fn (Get $get) => $get('jenis_pembayaran') === 'kredit')
                ->nullable(),
                
            Radio::make('jenis_penjualan')
                ->label('Jenis Penjualan')
                ->options([
                    'MARKETPLACE' => 'Marketplace',
                    'NON_MARKETPLACE' => 'Bukan Marketplace',
                ])
                ->default('NON_MARKETPLACE')
                ->required()
                ->reactive(),

            TextInput::make('no_pesanan')
                ->label('No Pesanan')
                ->visible(fn (Get $get) => $get('jenis_penjualan') === 'MARKETPLACE')
                ->required(fn (Get $get) => $get('jenis_penjualan') === 'MARKETPLACE'),

            Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(3)
                ->disabled(fn ($record) => $record?->isLocked()),

            Section::make('Ringkasan Faktur')
                ->schema([

                    Placeholder::make('subtotal_preview')
                        ->label('Subtotal')
                        ->content(function ($livewire) {
                            $subtotal = method_exists($livewire, 'getSubtotalProperty')
                                ? $livewire->getSubtotalProperty()
                                : 0;

                            if ($subtotal <= 0) {
                                return '⚠️ Belum ada barang ditambahkan';
                            }

                            return 'Rp ' . number_format($subtotal, 0, ',', '.');
                        }),

                    TextInput::make('diskon')
                        ->label('Diskon')
                        ->numeric()
                        ->default(0)
                        ->live(debounce: 500),

                    Placeholder::make('total_preview')
                        ->label('Total')
                        ->content(function (Get $get, $livewire) {
                            $subtotal = method_exists($livewire, 'getSubtotalProperty')
                                ? $livewire->getSubtotalProperty()
                                : 0;

                            if ($subtotal <= 0) {
                                return 'Total akan muncul setelah barang ditambahkan';
                            }

                            $diskon = (int) ($get('diskon') ?? 0);
                            $total = max($subtotal - $diskon, 0);

                            return 'Rp ' . number_format($total, 0, ',', '.');
                        }),

                ])
                ->columns(3)
                ->columnSpanFull(),
        ]);
    }
}
