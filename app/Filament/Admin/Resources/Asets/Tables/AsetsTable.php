<?php

namespace App\Filament\Admin\Resources\Asets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use App\Models\Penyusutan;
use Filament\Notifications\Notification;
use App\Models\Aset;


class AsetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_aset')
                    ->sortable()
                    ->searchable()
                    ->label('Kode Aset'),
                TextColumn::make('nama_aset')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Aset'),
                TextColumn::make('kategori_aset.nama_kategori') // relasi ditampilkan
                    ->label('Kategori Aset')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tanggal_perolehan')
                    ->date()
                    ->sortable()
                    ->label('Tanggal Perolehan'),
                TextColumn::make('nilai_perolehan')
                    ->money('idr', true)
                    ->sortable()
                    ->label('Nilai Perolehan'),
                TextColumn::make('nilai_residu')
                    ->money('idr', true)
                    ->sortable()
                    ->label('Nilai Residu'),
                TextColumn::make('masa_manfaat')
                    ->suffix(' tahun')
                    ->sortable()
                    ->label('Masa Manfaat'),
                TextColumn::make('metode_penyusutan')
                    ->sortable()
                    ->label('Metode Penyusutan'),
            ])

            ->filters([
                //
            ])
            ->actions([  // Ganti recordActions() ke actions()
                EditAction::make(),
                /*Action::make('susutkan')
                    ->label('Susutkan')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penyusutan')
                    ->modalDescription(fn ($record) => 
                        "Nilai Buku Saat Ini: Rp " . number_format($record->nilai_buku, 0, ',', '.')
                    )
                ->action(function (Aset $record) {

                $periode = now()->format('Y-m');

                // cegah double penyusutan
                if ($record->penyusutan()->where('periode', $periode)->exists()) {
                    Notification::make()
                        ->title('Gagal')
                        ->body('Penyusutan periode ini sudah ada')
                        ->danger()
                        ->send();
                    return;
                }

                $beban = $record->bebanPenyusutanTahunan();
                $akumulasi = $record->akumulasiTerakhir() + $beban;
                $nilaiBuku = max(
                    $record->nilai_perolehan - $akumulasi,
                    $record->nilai_residu
                );

                Penyusutan::create([
                    'aset_id' => $record->id,
                    'periode' => $periode,
                    'beban_penyusutan' => $beban,
                    'akumulasi_penyusutan' => $akumulasi,
                    'nilai_buku' => $nilaiBuku,
                ]);

                Notification::make()
                    ->title('Berhasil')
                    ->body('Nilai buku sekarang: Rp ' . number_format($nilaiBuku, 0, ',', '.'))
                    ->success()
                    ->send();
            })*/
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                   
                ]),
            ]);
    }
}
