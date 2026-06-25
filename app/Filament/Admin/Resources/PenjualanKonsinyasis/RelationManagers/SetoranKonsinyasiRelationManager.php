<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SetoranKonsinyasiRelationManager extends RelationManager
{
    protected static string $relationship = 'setoranKonsinyasi';

    /* =====================
     | FORM
     ===================== */
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('tanggal_setor')
                ->label('Tanggal Setor')
                ->default(now())
                ->required(),

            TextInput::make('jumlah_setor')
                ->label('Jumlah Setor')
                ->numeric()
                ->required()
                ->minValue(1)
                ->rule(function () {
                    return function (string $attribute, $value, $fail) {
                        $penjualan = $this->ownerRecord;
                        $sisa = $penjualan->total_terjual - $penjualan->total_setoran;

                        if ($value > $sisa) {
                            $fail('Jumlah setor melebihi sisa piutang.');
                        }
                    };
                }),

            Select::make('metode_bayar')
                ->label('Metode Bayar')
                ->options([
                    'transfer' => 'Transfer',
                ])
                ->required(),

            FileUpload::make('bukti_bayar')
                ->label('Bukti Bayar')
                ->directory('bukti-setoran')
                ->image()
                ->maxSize(2048),
        ]);
    }

    /* =====================
     | TABLE
     ===================== */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_setor')
                    ->label('Tanggal')
                    ->date(),

                Tables\Columns\TextColumn::make('jumlah_setor')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id_ID'),

                Tables\Columns\TextColumn::make('metode_bayar')
                    ->label('Metode'),

                Tables\Columns\ImageColumn::make('bukti_bayar')
                    ->label('Bukti Pembayaran'),
            ])

            ->headerActions([
                Action::make('tambah')
                    ->label('Tambah Setoran')
                    ->icon('heroicon-o-plus')
                    ->disabled(fn () => $this->ownerRecord->status === 'LUNAS')
                    
                    ->form(fn (Schema $schema) => $this->form($schema))
                    ->action(function (array $data) {
                        $penjualan = $this->ownerRecord;

                        if ($penjualan->status === 'LUNAS') {
                            throw new \Exception('Penjualan ini sudah lunas.');
                        }

                        $sisa = $penjualan->total_terjual - $penjualan->total_setoran;

                        if ($data['jumlah_setor'] > $sisa) {
                            throw new \Exception('Jumlah setor melebihi sisa piutang.');
                        }

                        $this->getRelationship()->create($data);
                    }),
            ])

            ->actions([
                EditAction::make()
                    ->disabled(fn ($record) => $record->penjualanKonsinyasi->status === 'LUNAS'),

                DeleteAction::make()
                    ->disabled(fn ($record) => $record->penjualanKonsinyasi->status === 'LUNAS'),
            ]);
    }
}
