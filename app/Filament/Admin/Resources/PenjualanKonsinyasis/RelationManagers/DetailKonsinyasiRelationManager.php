<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\RelationManagers;

use App\Models\Barang;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Illuminate\Validation\ValidationException;

class DetailKonsinyasiRelationManager extends RelationManager
{
    protected static string $relationship = 'detailKonsinyasi';

    protected static ?string $title = 'Barang Titipan Konsinyasi';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
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
                        $set('harga_konsinyasi', 0);
                        return;
                    }

                    $jenisMitra = $this->ownerRecord->mitra->jenisMitra ?? 'konsinyasi';

                    $harga = $barang->hargaBarang()
                        ->where('jenis_mitra', $jenisMitra)
                        ->value('harga') ?? 0;

                    $set('harga_konsinyasi', (int) $harga);
                    $set('qty_titip', 1);
                    $set('diskon', 0);
                }),

            TextInput::make('qty_titip')
                ->label('Qty Titip')
                ->numeric()
                ->required()
                ->default(1),

            TextInput::make('harga_konsinyasi')
                ->label('Harga Konsinyasi')
                ->numeric()
                ->required(),

            TextInput::make('diskon')
                ->label('Diskon (Rp)')
                ->numeric()
                ->default(0)
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('barang.nama_lengkap')
                    ->label('Barang'),

                TextColumn::make('qty_titip')
                    ->label('Qty Titip'),

                TextColumn::make('harga_konsinyasi')
                    ->label('Harga')
                    ->money('IDR', locale: 'id_ID'),

                TextColumn::make('diskon')
                    ->label('Diskon')
                    ->money('IDR', locale: 'id_ID'),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('IDR', locale: 'id_ID'),
            ])
            ->headerActions([
                Action::make('tambah')
                    ->label('Tambah Barang')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Tambah Barang Konsinyasi')
                    ->form(fn (Schema $schema) => $this->form($schema))
                    ->action(function (array $data) {
                        $barang = Barang::findOrFail($data['barang_id']);

                        if ((int) $data['qty_titip'] > (int) $barang->stok) {
                            throw ValidationException::withMessages([
                                'qty_titip' => "Stok {$barang->nama_lengkap} tidak mencukupi. Stok tersedia: {$barang->stok}",
                            ]);
                        }

                        $subtotalItem = ((int) $data['qty_titip'] * (int) $data['harga_konsinyasi']) - (int) ($data['diskon'] ?? 0);

                        if ($subtotalItem < 0) {
                            throw ValidationException::withMessages([
                                'diskon' => 'Diskon item tidak boleh melebihi subtotal barang.',
                            ]);
                        }

                        $data['subtotal'] = $subtotalItem;

                        $barang->decrement('stok', (int) $data['qty_titip']);

                        $this->getRelationship()->create($data);

                        $this->ownerRecord->hitungTotalKonsinyasi();
                        $this->ownerRecord->refresh();

                        // Auto-create Sales Order jika belum ada
                        if (!$this->ownerRecord->salesOrder) {
                            try {
                                \App\Models\SalesOrder::createFromPenjualanKonsinyasi($this->ownerRecord);
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error("Failed to create Sales Order: {$e->getMessage()}");
                            }
                        }

                        $this->dispatch('refreshPenjualanKonsinyasiSummary');
                    }),
            ]);
    }
}