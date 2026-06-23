<?php

namespace App\Filament\Admin\Resources\FakturPembelians\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;

class FakturPembelianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informasi Faktur')
                    ->description('Masukkan informasi dasar faktur pembelian')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('id_vendor')
                                    ->label('Vendor')
                                    ->relationship('vendor', 'nama_vendor')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('nama_vendor')
                                            ->label('Nama Vendor')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('kontak_vendor')
                                            ->label('Kontak Vendor')
                                            ->required()
                                            ->maxLength(20),
                                        Textarea::make('alamat_vendor')
                                            ->label('Alamat')
                                            ->rows(3),
                                    ])
                                    ->columnSpan(1),

                                DatePicker::make('tanggal_faktur')
                                    ->label('Tanggal Faktur')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),

                                Toggle::make('auto_nomor')
                                    ->label('ON/OFF Penomoran Otomatis')
                                    ->default(true)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        if ($state) {
                                            // ON → generate otomatis
                                            $set('no_faktur', 'FKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)));
                                        } else {
                                            // OFF → kosongkan biar user isi sendiri
                                            $set('no_faktur', null);
                                        }
                                    })
                                    ->columnSpan(1),

                                TextInput::make('no_faktur')
                                    ->label('No. Faktur')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->default(fn () => 'FKT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)))
                                    ->disabled(fn (Get $get): bool => (bool) $get('auto_nomor'))
                                    ->dehydrated() 
                                    ->columnSpan(1),

                                Placeholder::make('status_display')
                                    ->label('Status Pembayaran')
                                    ->content(fn ($record) => $record 
                                        ? match($record->status) {
                                            'belum_dibayar' => '🔴 Belum Dibayar',
                                            'belum_lunas' => '🟡 Belum Lunas',
                                            'lunas' => '🟢 Lunas',
                                            default => '🔴 Belum Dibayar'
                                        }
                                        : '🔴 Belum Dibayar '
                                    )
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columnSpan(2),

                Section::make('Detail Item Pembelian')
                    ->description('Daftar barang/aset yang dibeli')
                    ->schema([
                        Repeater::make('items')
    ->relationship()
    ->label('')
    ->schema([
        Grid::make(12)
            ->schema([
                // BARIS 1
                TextInput::make('nama_aset')
                    ->label('Nama Aset')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(6),

                Select::make('id_kategori')
                    ->label('Kategori')
                    ->relationship('kategoriAset', 'nama_kategori')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->columnSpan(6),

                // BARIS 2
                TextInput::make('qty')
                    ->label('Qty')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateItemTotal($get, $set);
                        self::updateGrandTotal($get, $set);
                    })
                    ->columnSpan(2),

                TextInput::make('keterangan')
                    ->label('Satuan')
                    ->placeholder('unit, pcs, kg, dll')
                    ->maxLength(100)
                    ->dehydrated()
                    ->columnSpan(4),

                TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateItemTotal($get, $set);
                        self::updateGrandTotal($get, $set);
                    })
                    ->columnSpan(6),

                // BARIS 3
                TextInput::make('total_harga')
                    ->label('Total')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly()
                    ->dehydrated()
                    ->columnSpan(4),
            ]),
    ])
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->deleteAction(
                                fn ($action) => $action->requiresConfirmation()
                            )
                            ->addActionLabel('Tambah Item')
                            ->columns(1)
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateGrandTotal($get, $set);
                            }),
                    ])
                    ->dehydrated(true)
                    ->columnSpan(2),

                Section::make('Biaya Tambahan')
                    ->description('Biaya lain-lain yang terkait pembelian')
                    ->schema([
                        TextInput::make('biaya_lain')
                            ->label('Biaya Lain')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                            ->minValue(0)
                            ->helperText('Biaya tambahan seperti ongkir, asuransi, dll')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::hitungTotal($get, $set);
                            }),
                    ])
                    ->columns(2)
                    ->columnSpan(1),

                Section::make('Ringkasan Tagihan')
                    ->description('Total yang harus dibayarkan')
                    ->schema([
                        Placeholder::make('subtotal_display')
                            ->label('Subtotal Items')
                            ->content(function (Get $get): string {
                                $items = $get('items') ?? [];
                                $subtotal = collect($items)->sum('total_harga');
                                return 'Rp ' . number_format($subtotal, 0, ',', '.');
                            }),

                        Placeholder::make('biaya_lain_display')
                            ->label('Biaya Lain')
                            ->content(function (Get $get): string {
                                $biayaLain = (float) ($get('biaya_lain') ?? 0);
                                return 'Rp ' . number_format($biayaLain, 0, ',', '.');
                            }),

                Placeholder::make('total_tagihan_display')
                    ->label('Total Tagihan')
                    ->content(function (Get $get): string {
                                $totalTagihan = (float) ($get('total_tagihan') ?? 0);
                                return 'Rp ' . number_format($totalTagihan, 0, ',', '.');
                            })
                    ->extraAttributes(['class' => 'text-2xl font-bold text-success-600']),

                                        
                Hidden::make('total_tagihan')
                    ->dehydrated()
                    ->reactive()
                    ->afterStateHydrated(function (Get $get, Set $set) {
                        self::hitungTotal($get, $set);
                    })
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::hitungTotal($get, $set);
                    })
                    ])
                    ->columnSpan(1),
            ]);
    }

    protected static function updateItemTotal(Get $get, Set $set): void
    {
        $qty = (float) ($get('qty') ?? 0);
        $hargaSatuan = (float) ($get('harga_satuan') ?? 0);
        $totalHarga = $qty * $hargaSatuan;
        
        $set('total_harga', $totalHarga);
        self::hitungTotal($get, $set);
    }

        protected static function hitungTotal(Get $get, Set $set): void
    {
        $items = $get('items') ?? [];
        $subtotal = collect($items)->sum('total_harga');
        $biayaLain = (float) ($get('biaya_lain') ?? 0);
        $totalTagihan = $subtotal + $biayaLain;

        $set('total_tagihan', $totalTagihan);
    }

    protected static function updateGrandTotal(Get $get, Set $set): void
{
    $items = $get('../../items') ?? [];
    $subtotal = collect($items)->sum('total_harga');

    $biayaLain = (float) ($get('../../biaya_lain') ?? 0);

    $set('../../total_tagihan', $subtotal + $biayaLain);
}

}