<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\RelationManagers;

use App\Models\Barang;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;
use App\Filament\Admin\Resources\LaporanKonsinyasis\Pages\ViewLaporanKonsinyasi;

class DetailLaporanRelationManager extends RelationManager
{
    protected static string $relationship = 'detailLaporan';

    protected static ?string $title = 'Detail Barang Terjual';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('barang_id')
                ->label('Barang')
                ->options(function () {
                    $laporan = $this->ownerRecord;
                    $penjualan = $laporan->penjualanKonsinyasi;

                    if (! $penjualan) {
                        return [];
                    }

                    return $penjualan->detailKonsinyasi()
                        ->with('barang.kategori')
                        ->get()
                        ->mapWithKeys(function ($detail) {
                            return [
                                $detail->barang_id => $detail->barang->nama_lengkap,
                            ];
                        })
                        ->toArray();
                })
                ->searchable()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    $laporan = $this->ownerRecord;
                    $penjualan = $laporan->penjualanKonsinyasi;

                    if (! $penjualan) {
                        $set('harga_konsinyasi', 0);
                        $set('qty_terjual', 1);
                        $set('subtotal', 0);
                        return;
                    }

                    $detailTitipan = $penjualan->detailKonsinyasi()
                        ->where('barang_id', $state)
                        ->first();

                    if (! $detailTitipan) {
                        $set('harga_konsinyasi', 0);
                        $set('qty_terjual', 1);
                        $set('subtotal', 0);
                        return;
                    }

                    $harga = (int) $detailTitipan->harga_konsinyasi;

                    $set('harga_konsinyasi', $harga);
                    $set('qty_terjual', 1);
                    $set('subtotal', $harga);
                }),

            TextInput::make('qty_terjual')
                ->label('Qty Terjual')
                ->numeric()
                ->required()
                ->default(1)
                ->live()
                ->reactive()
                ->helperText(function ($get) {
                    $barangId = $get('barang_id');
                    $laporan = $this->ownerRecord;
                    $penjualan = $laporan->penjualanKonsinyasi;

                    if (! $barangId || ! $penjualan) return null;

                    $detailTitipan = $penjualan->detailKonsinyasi()
                        ->where('barang_id', $barangId)
                        ->first();

                    if (! $detailTitipan) return null;

                    $qtyTitip = (int) $detailTitipan->qty_titip;

                    $qtySudahDilaporkan = \App\Models\DetailLaporanKonsinyasi::query()
                        ->whereIn('no_laporan', $penjualan->laporanKonsinyasi()->pluck('no_laporan'))
                        ->where('barang_id', $barangId)
                        ->sum('qty_terjual');

                    $sisa = max($qtyTitip - $qtySudahDilaporkan, 0);

                    return "Sisa qty: $sisa";
                })
                ->afterStateUpdated(function ($state, callable $get, callable $set, $livewire) {

                    $barangId = $get('barang_id');
                    $laporan = $this->ownerRecord;
                    $penjualan = $laporan->penjualanKonsinyasi;

                    if (! $barangId || ! $penjualan) return;

                    $detailTitipan = $penjualan->detailKonsinyasi()
                        ->where('barang_id', $barangId)
                        ->first();

                    if (! $detailTitipan) return;

                    $qtyTitip = (int) $detailTitipan->qty_titip;

                    $qtySudahDilaporkan = \App\Models\DetailLaporanKonsinyasi::query()
                        ->whereIn('no_laporan', $penjualan->laporanKonsinyasi()->pluck('no_laporan'))
                        ->where('barang_id', $barangId)
                        ->sum('qty_terjual');

                    $sisa = max($qtyTitip - $qtySudahDilaporkan, 0);

                    if ((int) $state > $sisa) {
                        $livewire->addError('data.qty_terjual', "Qty melebihi sisa. Maksimal: $sisa");
                    } else {
                        $livewire->resetErrorBag('data.qty_terjual');
                    }

                    // sekalian update subtotal di sini
                    $harga = (int) $get('harga_konsinyasi');
                    $set('subtotal', $harga * (int) $state);
                }),
            TextInput::make('harga_konsinyasi')
    ->label('Harga')
                ->numeric()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                    $qty = (int) $get('qty_terjual');
                    $set('subtotal', $qty * (int) $state);
                }),

            TextInput::make('subtotal')
                ->label('Subtotal')
                ->disabled()
                ->dehydrated(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barang.nama_lengkap')->label('Barang'),
                Tables\Columns\TextColumn::make('qty_terjual')->label('Qty'),
                Tables\Columns\TextColumn::make('harga_konsinyasi')->label('Harga')->money('IDR', locale: 'id_ID'),
                Tables\Columns\TextColumn::make('subtotal')->label('Subtotal')->money('IDR', locale: 'id_ID'),
            ])
            ->headerActions([
            Action::make('tambah')
                ->label('Tambah Barang')
                ->icon('heroicon-o-plus')
                ->visible(fn () => $this->getPageClass() !== ViewLaporanKonsinyasi::class)
                ->form(fn (Schema $schema) => $this->form($schema))
                ->action(function (array $data) {
                $laporan = $this->ownerRecord;
                $penjualan = $laporan->penjualanKonsinyasi;

                if (! $penjualan) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'barang_id' => 'Data konsinyasi tidak ditemukan.',
                    ]);
                }

                $detailTitipan = $penjualan->detailKonsinyasi()
                    ->where('barang_id', $data['barang_id'])
                    ->first();

                if (! $detailTitipan) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'barang_id' => 'Barang ini tidak termasuk dalam barang titipan konsinyasi.',
                    ]);
                }

                $qtyTitip = (int) $detailTitipan->qty_titip;

                $qtySudahDilaporkan = \App\Models\DetailLaporanKonsinyasi::query()
                    ->whereIn('no_laporan', $penjualan->laporanKonsinyasi()->pluck('no_laporan'))
                    ->where('barang_id', $data['barang_id'])
                    ->sum('qty_terjual');

                $qtyBaru = (int) $data['qty_terjual'];
                $totalQtySetelahTambah = $qtySudahDilaporkan + $qtyBaru;

                if ($totalQtySetelahTambah > $qtyTitip) {
                    $sisaQty = max($qtyTitip - $qtySudahDilaporkan, 0);

                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'qty_terjual' => 'Qty terjual melebihi qty titipan. Sisa qty yang masih bisa dilaporkan: ' . $sisaQty,
                    ]);
                }

                $subtotalItem = (int) $data['qty_terjual'] * (int) $data['harga_konsinyasi'];

                // Ambil HPP dari detail_konsinyasi yang sudah menyimpan harga_modal_per_pack
                // hasil FEFO saat barang dititipkan ke mitra
                $hargaModalPerPack = (float) ($detailTitipan->harga_modal_per_pack ?? 0);
                $subtotalHpp       = round($hargaModalPerPack * $qtyBaru, 2);

                $data['no_laporan']          = $laporan->no_laporan;
                $data['subtotal']            = $subtotalItem;
                $data['harga_modal_per_pack'] = $hargaModalPerPack;
                $data['subtotal_hpp']         = $subtotalHpp;

                $this->getRelationship()->create($data);

                $laporan->refreshTotalLaporan();
                $laporan->refresh();
                $this->dispatch('updateTotalLaporan');
            }),
        ])
        ->actions([
            DeleteAction::make()
                ->visible(fn () => $this->getPageClass() !== ViewLaporanKonsinyasi::class)
                ->after(function () {
                    $this->ownerRecord->refreshTotalLaporan();
                    $this->ownerRecord->refresh();
                    $this->dispatch('updateTotalLaporan');
                }),
        ]);
    }
}