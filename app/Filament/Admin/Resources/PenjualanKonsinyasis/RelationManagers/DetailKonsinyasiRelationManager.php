<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\RelationManagers;

use App\Filament\Support\MoneyInput;
use App\Models\Barang;
use App\Models\DetailKonsinyasi;
use App\Models\DetailPersediaanProduk;
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

class DetailKonsinyasiRelationManager extends RelationManager
{
    protected static string $relationship = 'detailKonsinyasi';
    protected static ?string $title = 'Barang Titipan Konsinyasi';

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
                                    $set('harga_konsinyasi', 0);
                                    $set('qty_titip', 1);
                                    $set('diskon', 0);
                                    $set('subtotal', 0);
                                    return;
                                }

                                $jenisMitra = $this->ownerRecord->mitra->jenisMitra ?? 'konsinyasi';

                                $harga = $barang->hargaBarang()
                                    ->where('jenis_mitra', $jenisMitra)
                                    ->value('harga') ?? 0;

                                $set('harga_konsinyasi', (int) $harga);
                                $set('qty_titip', 1);
                                $set('diskon', 0);
                                $set('subtotal', (int) $harga);
                            })
                            ->columnSpanFull(),

                        TextInput::make('qty_titip')
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
                                    $set('qty_titip', 1);
                                }
                                $harga  = (int) ($get('harga_konsinyasi') ?? 0);
                                $diskon = (int) ($get('diskon') ?? 0);
                                $set('subtotal', max(($harga * (int) $state) - $diskon, 0));
                            }),

                        MoneyInput::make('harga_konsinyasi')
                            ->label('Harga')
                            ->required()
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $qty    = (int) ($get('qty_titip') ?? 0);
                                $diskon = (int) str_replace('.', '', $get('diskon') ?? '0');
                                $harga  = (int) str_replace('.', '', $state ?? '0');
                                $set('subtotal', max(($harga * $qty) - $diskon, 0));
                            }),

                        MoneyInput::make('diskon')
                            ->label('Diskon')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $harga  = (int) str_replace('.', '', $get('harga_konsinyasi') ?? '0');
                                $qty    = (int) ($get('qty_titip') ?? 0);
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
                TextColumn::make('qty_titip')->label('Qty Titip'),
                TextColumn::make('harga_konsinyasi')->label('Harga')->money('IDR', locale: 'id_ID'),
                TextColumn::make('diskon')->label('Diskon')->money('IDR', locale: 'id_ID'),
                TextColumn::make('subtotal')->label('Subtotal')->money('IDR', locale: 'id_ID'),
            ])
            ->headerActions([
                Action::make('tambah')
                    ->label('Tambah Barang')
                    ->icon('heroicon-o-plus')
                    ->visible(fn () => ! $this->isReadOnly())
                    ->modalHeading('Tambah Barang Konsinyasi')
                    ->modalWidth('lg')
                    ->form(fn (Schema $schema) => $this->form($schema))
                    ->action(function (array $data) {
                        try {
                            $barang = Barang::findOrFail($data['barang_id']);

                            $stokTersedia = $barang->getStokTersedia();
                            if ((int) $data['qty_titip'] > $stokTersedia) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Stok Tidak Mencukupi')
                                    ->body("Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$stokTersedia}")
                                    ->send();
                                throw new \Filament\Support\Exceptions\Halt();
                            }

                            $subtotalItem = ((int) $data['qty_titip'] * (int) str_replace('.', '', $data['harga_konsinyasi'] ?? '0')) - (int) str_replace('.', '', $data['diskon'] ?? '0');

                            if ($subtotalItem < 0) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Diskon Tidak Valid')
                                    ->body('Diskon item tidak boleh melebihi subtotal barang.')
                                    ->send();
                                throw new \Filament\Support\Exceptions\Halt();
                            }

                            $mitra = $this->ownerRecord->mitra;
                            if ($mitra && $mitra->limit_piutang > 0) {
                                $limit        = (float) $mitra->limit_piutang;
                                $aktif        = (float) $mitra->piutang_aktif_konsinyasi;
                                $totalSaatIni = (float) $this->ownerRecord->total_barang;

                                if (($aktif + $totalSaatIni + $subtotalItem) > $limit) {
                                    $sisaLimit = max($limit - $aktif - $totalSaatIni, 0);
                                    \Filament\Notifications\Notification::make()
                                        ->danger()
                                        ->title('Limit Piutang Terlampaui')
                                        ->body('Limit piutang terlampaui. Sisa kuota untuk transaksi ini: Rp ' . number_format($sisaLimit, 0, ',', '.'))
                                        ->send();
                                    throw new \Filament\Support\Exceptions\Halt();
                                }
                            }

                            $data['subtotal'] = $subtotalItem;

                            DB::transaction(function () use (&$data) {
                                $hpp = DetailPersediaanProduk::kurangiStokFefo(
                                    (int) $data['barang_id'],
                                    (int) $data['qty_titip']
                                );
                                $data['harga_modal_per_pack'] = $hpp['harga_modal_per_pack'];
                                $data['subtotal_hpp']         = $hpp['subtotal_hpp'];
                                $this->getRelationship()->create($data);
                            });

                            $this->ownerRecord->hitungTotalKonsinyasi();
                            $this->ownerRecord->refresh();

                            if (! $this->ownerRecord->salesOrder) {
                                try {
                                    \App\Models\SalesOrder::createFromPenjualanKonsinyasi($this->ownerRecord);
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error("Failed to create Sales Order: {$e->getMessage()}");
                                }
                            }

                            $this->dispatch('refreshPenjualanKonsinyasiSummary');

                        } catch (\Filament\Support\Exceptions\Halt $e) {
                            throw $e;
                        } catch (\RuntimeException $e) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Gagal Mengurangi Stok')
                                ->body($e->getMessage())
                                ->send();
                            throw new \Filament\Support\Exceptions\Halt();
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::error('DetailKonsinyasi tambah error: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Terjadi Kesalahan')
                                ->body($e->getMessage())
                                ->send();
                            throw new \Filament\Support\Exceptions\Halt();
                        }
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->using(function ($record, array $data) {
                        $qtyLama = (int) DetailKonsinyasi::query()
                            ->where('id', $record->id)
                            ->value('qty_titip');

                        $qtyBaru = (int) $data['qty_titip'];
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

                        $subtotalItemLama = (int) $record->subtotal;
                        $subtotalItemBaru = ((int) $data['qty_titip'] * (int) str_replace('.', '', $data['harga_konsinyasi'] ?? '0')) - (int) str_replace('.', '', $data['diskon'] ?? '0');
                        $selisihSubtotal = $subtotalItemBaru - $subtotalItemLama;

                        if ($selisihSubtotal > 0 && $this->ownerRecord->mitra) {
                            $mitra = $this->ownerRecord->mitra;
                            if ($mitra->limit_piutang > 0) {
                                $limit        = (float) $mitra->limit_piutang;
                                $aktif        = (float) $mitra->piutang_aktif_konsinyasi;
                                $totalSaatIni = (float) $this->ownerRecord->total_barang;

                                if (($aktif + $totalSaatIni + $selisihSubtotal) > $limit) {
                                    $sisaLimit = max($limit - $aktif - $totalSaatIni, 0);
                                    \Filament\Notifications\Notification::make()
                                        ->danger()
                                        ->title('Limit Piutang Terlampaui')
                                        ->body('Limit piutang terlampaui dengan penambahan qty/harga ini. Sisa kuota: Rp ' . number_format($sisaLimit, 0, ',', '.'))
                                        ->send();
                                    throw new \Filament\Support\Exceptions\Halt();
                                }
                            }
                        }

                        $data['subtotal'] = $subtotalItemBaru;

                        DB::transaction(function () use ($record, $data, $selisih, $qtyBaru, $qtyLama) {
                            if ($selisih > 0) {
                                // Qty bertambah → kurangi stok tambahan, ambil HPP selisih
                                $hppSelisih = DetailPersediaanProduk::kurangiStokFefo(
                                    (int) $record->barang_id,
                                    $selisih
                                );

                                $subtotalHppBaru = (float) $record->subtotal_hpp + $hppSelisih['subtotal_hpp'];

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
                    }),

                DeleteAction::make()
                    ->using(function ($record) {
                        DB::transaction(function () use ($record) {
                            DetailPersediaanProduk::kembalikanStokFefo(
                                (int) $record->barang_id,
                                (int) $record->qty_titip
                            );
                            $record->delete();
                        });
                    }),
            ]);
    }

    public function isReadOnly(): bool
    {
        return $this->getPageClass() === \App\Filament\Admin\Resources\PenjualanKonsinyasis\Pages\ViewPenjualanKonsinyasi::class;
    }
}
