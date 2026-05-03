<?php

namespace App\Filament\Admin\Resources\PenjualanNonKonsinyasis\RelationManagers;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Barang;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn; 
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Validation\ValidationException;

class DetailPenjualanNonKonsinyasiRelationManager extends RelationManager
{
    protected static string $relationship = 'detailPenjualan';
    protected static ?string $title = 'Detail Barang';

    /* =====================
     | FORM DETAIL BARANG
     ===================== */
    public function form(Schema $schema): Schema
{
    return $schema->components([
        Section::make('Detail Barang')
            ->description('Masukkan barang yang dijual')
            ->icon('heroicon-o-cube')
            ->schema([
                Grid::make(2)->schema([

                    Select::make('barang_id')
                        ->label('Barang')
                        ->options(fn () => Barang::with('kategori')
                            ->get()
                            ->mapWithKeys(fn ($barang) => [
                                $barang->id => $barang->nama_lengkap,
                            ])
                            ->toArray()
                        )
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
                        ->live()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $harga = (int) ($get('harga') ?? 0);
                            $diskon = (int) ($get('diskon') ?? 0);

                            $subtotal = ($harga * (int) $state) - $diskon;

                            $set('subtotal', max($subtotal, 0));
                        }),

                    TextInput::make('harga')
                        ->label('Harga')
                        ->numeric()
                        ->required()
                        ->dehydrated()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $qty = (int) ($get('qty') ?? 0);
                            $diskon = (int) ($get('diskon') ?? 0);

                            $subtotal = ((int) $state * $qty) - $diskon;

                            $set('subtotal', max($subtotal, 0));
                        }),

                    TextInput::make('diskon')
                        ->label('Diskon')
                        ->numeric()
                        ->default(0)
                        ->live()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $harga = (int) ($get('harga') ?? 0);
                            $qty = (int) ($get('qty') ?? 0);

                            $subtotal = ($harga * $qty) - (int) $state;

                            $set('subtotal', max($subtotal, 0));
                        }),

                    TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->numeric()
                        ->disabled()
                        ->dehydrated(),
                ]),
            ]),
    ]);
}
    /* =====================
     | TABLE DETAIL BARANG
     ===================== */
    public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('barang.nama_lengkap')->label('Barang'),
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

                if ($data['qty'] > $barang->stok) {
                    throw ValidationException::withMessages([
                        'qty' => "Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$barang->stok}",
                    ]);
                }

                $data['diskon'] = (int) ($data['diskon'] ?? 0);

                $subtotalItem = ((int) $data['qty'] * (int) $data['harga']) - $data['diskon'];

                if ($subtotalItem < 0) {
                    throw ValidationException::withMessages([
                        'diskon' => 'Diskon tidak boleh melebihi subtotal barang.',
                    ]);
                }

                $data['subtotal'] = $subtotalItem;

                $barang->decrement('stok', $data['qty']);

                $detail = $this->getRelationship()->create($data);

                $detail->penjualan->hitungTotal();
                $detail->penjualan->refresh();

                // Auto-create Sales Order jika belum ada
                if (!$detail->penjualan->salesOrder) {
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
                ->after(function ($record, array $data) {
                    $selisih = $data['qty'] - $record->getOriginal('qty');

                    if ($selisih !== 0) {
                        $record->barang->decrement('stok', $selisih);
                    }

                    $record->penjualan->hitungTotal();
                }),

            DeleteAction::make()
                ->after(function ($record) {
                    $record->barang->increment('stok', $record->qty);
                    $record->penjualan->hitungTotal();
                }),
        ]);
}

    public function isReadOnly(): bool
    {
        return $this->ownerRecord->status === 'LUNAS';
    }

}
