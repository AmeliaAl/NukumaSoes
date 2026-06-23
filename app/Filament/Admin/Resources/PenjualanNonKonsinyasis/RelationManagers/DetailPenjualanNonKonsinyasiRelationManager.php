<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\RelationManagers;

use App\Filament\Support\MoneyInput;
use App\Models\Barang;
use App\Models\DetailPersediaanProduk;
use App\Models\DetailPenjualanNonKonsinyasi;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DetailPenjualanNonKonsinyasiRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPenjualan';
    protected static ?string $title = 'Detail Barang';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Barang')
                ->description('Masukkan barang yang dijual')
                ->icon('heroicon-o-cube')
                ->schema([
                    Grid::make(2)->schema([

                        Select::make('kategori_filter')
                            ->label('Kategori')
                            ->options(function () {
                                // Hanya kategori yang barangnya punya stok tersedia & belum expired
                                $barangIdTersedia = \App\Models\DetailPersediaanProduk::where('stok_saat_ini', '>', 0)
                                    ->where(function ($q) {
                                        $q->whereNull('tanggal_expired')
                                            ->orWhere('tanggal_expired', '>=', now()->toDateString());
                                    })
                                    ->pluck('barang_id')
                                    ->unique();

                                return \App\Models\Kategori::whereHas('barang', function ($q) use ($barangIdTersedia) {
                                    $q->whereIn('id', $barangIdTersedia);
                                })
                                    ->orderBy('nama_kategori')
                                    ->pluck('nama_kategori', 'id');
                            })
                            ->searchable()
                            ->live()
                            ->dehydrated(false)
                            ->placeholder('Semua Kategori')
                            ->columnSpanFull(),

                        Select::make('barang_id')
                            ->label('Barang')
                            ->options(function ($get) {
                                // Ambil barang_id yang punya stok tersedia & belum expired
                                $barangIdTersedia = \App\Models\DetailPersediaanProduk::where('stok_saat_ini', '>', 0)
                                    ->where(function ($q) {
                                        $q->whereNull('tanggal_expired')
                                            ->orWhere('tanggal_expired', '>=', now()->toDateString());
                                    })
                                    ->pluck('barang_id')
                                    ->unique();

                                $query = \App\Models\Barang::whereIn('id', $barangIdTersedia)
                                    ->orderBy('nama_barang');

                                if ($get('kategori_filter')) {
                                    $query->where('kategori_id', $get('kategori_filter'));
                                }

                                return $query->get()->mapWithKeys(function ($barang) {
                                    $stok = $barang->getStokTersedia();
                                    return [$barang->id => "{$barang->nama_barang} (Stok: {$stok})"];
                                });
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $barang = Barang::with('hargaBarang', 'kategori')->find($state);

                                if (! $barang) {
                                    $set('harga', 0);
                                    $set('qty', 1);
                                    $set('diskon', 0);
                                    $set('subtotal', 0);
                                    return;
                                }

                                $harga = $barang->hargaBarang()
                                    ->where('jenis_mitra', 'umum')
                                    ->value('harga');

                                if (! $harga) {
                                    $harga = $barang->hargaBarang()
                                        ->where('jenis_mitra', 'reseller')
                                        ->value('harga') ?? 0;
                                }

                                $set('harga', (int) $harga);
                                $set('qty', 1);
                                $set('diskon', 0);
                                $set('subtotal', (int) $harga);
                            })
                            ->columnSpanFull(),

                        TextInput::make('qty')
                            ->label('Qty')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->extraInputAttributes([
                                'oninput' => "this.value = this.value.replace(/[^0-9]/g, ''); if(this.value === '0') this.value = '1';"
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                if ($state !== null && (int) $state < 1) {
                                    $state = 1;
                                    $set('qty', 1);
                                }
                                $harga  = (int) ($get('harga') ?? 0);
                                $diskon = (int) ($get('diskon') ?? 0);
                                $set('subtotal', max(($harga * (int) $state) - $diskon, 0));
                            }),

                        MoneyInput::make('harga')
                            ->label('Harga')
                            ->required()
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $qty    = (int) ($get('qty') ?? 0);
                                $diskon = (int) str_replace('.', '', $get('diskon') ?? '0');
                                $harga  = (int) str_replace('.', '', $state ?? '0');
                                $set('subtotal', max(($harga * $qty) - $diskon, 0));
                            }),

                        MoneyInput::make('diskon')
                            ->label('Diskon')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $harga  = (int) str_replace('.', '', $get('harga') ?? '0');
                                $qty    = (int) ($get('qty') ?? 0);
                                $diskon = (int) str_replace('.', '', $state ?? '0');
                                $set('subtotal', max(($harga * $qty) - $diskon, 0));
                            }),

                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(),
                    ]),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.kategori.nama_kategori')->label('Kategori'),
                TextColumn::make('barang.nama_barang')->label('Barang'),
                TextColumn::make('qty')->label('Qty'),
                TextColumn::make('harga')->money('IDR', locale: 'id_ID'),
                TextColumn::make('diskon')->label('Diskon')->money('IDR', locale: 'id_ID'),
                TextColumn::make('subtotal')->money('IDR', locale: 'id_ID'),
            ])
            ->headerActions([
                Action::make('tambah')
                    ->label('Tambah Barang')
                    ->icon('heroicon-o-plus')
                    ->disabled(fn () => $this->ownerRecord->isLocked())
                    ->modalHeading('Tambah Barang')
                    ->modalWidth('lg')
                    ->form(fn (Schema $schema) => $this->form($schema))
                    ->action(function (array $data) {
                        $barang = Barang::findOrFail($data['barang_id']);

                        $stokTersedia = $barang->getStokTersedia();
                        if ((int) $data['qty'] > $stokTersedia) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Stok Tidak Mencukupi')
                                ->body("Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$stokTersedia}")
                                ->send();
                            throw new \Filament\Support\Exceptions\Halt();
                        }

                        $data['diskon']   = (int) str_replace('.', '', $data['diskon'] ?? '0');
                        $subtotalItem     = ((int) $data['qty'] * (int) str_replace('.', '', $data['harga'] ?? '0')) - $data['diskon'];

                        if ($subtotalItem < 0) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Diskon Tidak Valid')
                                ->body('Diskon tidak boleh melebihi subtotal barang.')
                                ->send();
                            throw new \Filament\Support\Exceptions\Halt();
                        }

                        $data['subtotal'] = $subtotalItem;

                        try {
                            $detail = DB::transaction(function () use (&$data) {
                                // Kurangi stok FEFO — return nilai HPP dari batch yang keluar
                                $hpp = DetailPersediaanProduk::kurangiStokFefo(
                                    (int) $data['barang_id'],
                                    (int) $data['qty']
                                );

                                // Simpan HPP ke detail transaksi
                                $data['harga_modal_per_pack'] = $hpp['harga_modal_per_pack'];
                                $data['subtotal_hpp']         = $hpp['subtotal_hpp'];

                                return $this->getRelationship()->create($data);
                            });
                        } catch (\RuntimeException $e) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->send();
                            throw new \Filament\Support\Exceptions\Halt();
                        }

                        $detail->penjualan->hitungTotal();
                        $detail->penjualan->refresh();

                        if (! $detail->penjualan->salesOrder) {
                            try {
                                \App\Models\SalesOrder::createFromPenjualanNonKonsinyasi($detail->penjualan);
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Failed to create Sales Order: {$e->getMessage()}");
                            }
                        }

                        $this->dispatch('refreshPenjualanNonKonsinyasiSummary');
                        $this->dispatch('refreshHeaderActions');
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->using(function ($record, array $data) {
                        $qtyLama = (int) DetailPenjualanNonKonsinyasi::query()
                            ->where('id', $record->id)
                            ->value('qty');

                        $qtyBaru = (int) $data['qty'];
                        $selisih = $qtyBaru - $qtyLama;

                        if ($selisih > 0) {
                            $barang = Barang::find($record->barang_id);
                            $stokTersedia = $barang ? $barang->getStokTersedia() : 0;
                            if ($selisih > $stokTersedia) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Stok Tidak Mencukupi')
                                    ->body("Stok {$barang->nama_barang} tidak mencukupi untuk penambahan. Stok tersedia: {$stokTersedia}")
                                    ->send();
                                throw new \Filament\Support\Exceptions\Halt();
                            }
                        }

                        DB::transaction(function () use ($record, $data, $selisih, $qtyBaru) {
                            if ($selisih > 0) {
                                // Qty bertambah → kurangi stok tambahan, ambil HPP selisih
                                $hppSelisih = DetailPersediaanProduk::kurangiStokFefo(
                                    (int) $record->barang_id,
                                    $selisih
                                );

                                // Hitung ulang HPP rata-rata tertimbang gabungan lama + tambahan
                                $subtotalHppLama = (float) $record->subtotal_hpp;
                                $subtotalHppBaru = $subtotalHppLama + $hppSelisih['subtotal_hpp'];

                                $data['subtotal_hpp']         = round($subtotalHppBaru, 2);
                                $data['harga_modal_per_pack'] = $qtyBaru > 0
                                    ? round($subtotalHppBaru / $qtyBaru, 2)
                                    : 0;

                            } elseif ($selisih < 0) {
                                // Qty berkurang → kembalikan stok, kurangi subtotal_hpp proporsional
                                DetailPersediaanProduk::kembalikanStokFefo(
                                    (int) $record->barang_id,
                                    abs($selisih)
                                );

                                // Kurangi subtotal_hpp secara proporsional
                                $hppPerUnit      = $qtyLama > 0
                                    ? ((float) $record->subtotal_hpp / $qtyLama)
                                    : 0;
                                $subtotalHppBaru = round($hppPerUnit * $qtyBaru, 2);

                                $data['subtotal_hpp']         = $subtotalHppBaru;
                                $data['harga_modal_per_pack'] = $qtyBaru > 0
                                    ? round($subtotalHppBaru / $qtyBaru, 2)
                                    : 0;
                            }
                            // Jika selisih = 0, HPP tidak berubah — biarkan nilai lama

                            $record->update($data);
                        });

                        return $record;
                    })
                    ->after(function ($record) {
                        $record->penjualan->hitungTotal();
                    }),

                DeleteAction::make()
                    ->using(function ($record) {
                        DB::transaction(function () use ($record) {
                            DetailPersediaanProduk::kembalikanStokFefo(
                                (int) $record->barang_id,
                                (int) $record->qty
                            );
                            $record->delete();
                        });
                    })
                    ->after(function ($record) {
                        $record->penjualan->hitungTotal();
                    }),
            ]);
    }

    public function isReadOnly(): bool
    {
        return $this->ownerRecord->status === 'LUNAS';
    }
}
