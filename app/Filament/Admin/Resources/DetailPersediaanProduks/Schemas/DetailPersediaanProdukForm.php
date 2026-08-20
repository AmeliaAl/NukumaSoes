<?php

namespace App\Filament\Admin\Resources\DetailPersediaanProduks\Schemas;

use App\Models\Barang;
use App\Models\Kategori;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DetailPersediaanProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Persediaan Produk')
                    ->schema([
                        // ── Baris 1: Kategori + Barang (cascade) ─────────
                        Select::make('kategori_filter')
                            ->label('Kategori')
                            ->options(fn () => Kategori::orderBy('nama_kategori')->pluck('nama_kategori', 'id')->toArray())
                            ->searchable()
                            ->required()
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(fn (callable $set) => $set('barang_id', null))
                            ->afterStateHydrated(function (callable $set, $record) {
                                if (! $record?->barang_id) return;
                                $barang = Barang::find($record->barang_id);
                                $set('kategori_filter', $barang?->kategori_id);
                            }),

                        Select::make('barang_id')
                            ->label('Nama Barang')
                            ->options(function (callable $get) {
                                $kategoriId = $get('kategori_filter');
                                if (! $kategoriId) return [];
                                return Barang::where('kategori_id', $kategoriId)
                                    ->orderBy('nama_barang')
                                    ->pluck('nama_barang', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, $record) {
                                if (! $record) {
                                    $set('stok_saat_ini', 0);
                                }
                            }),

                        // ── Baris 2: Batch (full width) ───────────────────
                        TextInput::make('batch')
                            ->label('Batch')
                            ->required()
                            ->placeholder('Contoh: BATCH-001'),

                        // ── Baris 3: Stok Awal (stok_saat_ini hidden) ─────
                        TextInput::make('stok_awal')
                            ->label('Stok Awal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        TextInput::make('harga_modal_per_pack')
                            ->label('Harga Modal / HPP per Pack (Rp)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->prefix('Rp')
                            ->helperText('HP produksi per pack — bukan harga jual.'),

                        // ── Baris 5: Tanggal Expired ──────────────────────
                        DatePicker::make('tanggal_expired')
                            ->label('Tanggal Expired')
                            ->nullable()
                            ->displayFormat('d/m/Y'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
