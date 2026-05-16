<?php

namespace App\Filament\Admin\Resources\Pemeliharaans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\{
    TextInput,
    Select,
    DatePicker,
    Textarea
};
use Filament\Schemas\Components\Utilities\Get;
use App\Models\Akun;
use App\Models\JurnalDetail;

class PemeliharaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pemeliharaan')
                    ->description('Isi data pemeliharaan aset yang dilakukan')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columnSpanFull()
                    ->schema([

                        Grid::make(2)->schema([

                            Select::make('aset_id')
                                ->label('Aset')
                                ->relationship('aset', 'nama_aset')
                                ->getOptionLabelFromRecordUsing(
                                    fn ($record) => "{$record->kode_aset} - {$record->nama_aset}"
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false)
                                ->placeholder('Pilih aset'),

                            DatePicker::make('tanggal')
                                ->label('Tanggal Pemeliharaan')
                                ->required()
                                ->default(now())
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->maxDate(now()),

                            Select::make('jenis_perbaikan')
                                ->label('Jenis Perbaikan')
                                ->options([
                                    'maintenance' => 'Maintenance',
                                    'peningkatan' => 'Peningkatan',
                                ])
                                ->required()
                                ->native(false)
                                ->placeholder('Pilih jenis perbaikan'),

                            TextInput::make('tambah_umur')
                                ->label('Tambah Umur (bulan)')
                                ->numeric()
                                ->live()
                                ->visible(fn (Get $get) => $get('jenis_perbaikan') === 'peningkatan'),

                            Select::make('metode_pembayaran')
                                ->label('Metode Pembayaran')
                                ->options([
                                    'cash' => 'Cash',
                                    'transfer' => 'Transfer Bank',
                                ])
                                ->default('cash')
                                ->required()
                                ->live()
                                ->native(false),

                            Select::make('id_akun')
                                ->label('Bayar Dari Akun')
                                ->relationship(
                                    'akun',
                                    'nama_akun',
                                    fn ($query) => $query->where('nama_akun', 'like', 'kas%')
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) =>
                                    "{$record->nama_akun} ({$record->no_akun}) - Rp " .
                                    number_format(\App\Helpers\AkunHelper::getSaldo($record->id), 0, ',', '.')
                                )
                                ->searchable()
                                ->required()
                                ->visible(fn (Get $get) => $get('metode_pembayaran') === 'transfer'),

                            TextInput::make('biaya')
                                ->label('Biaya Pemeliharaan')
                                ->numeric()
                                ->required()
                                ->prefix('Rp')
                                ->placeholder('0')
                                ->minValue(0)
                                ->afterStateUpdated(function ($state, callable $set, Get $get) {

                                    $akunId = $get('id_akun');
                                    $metode = $get('metode_pembayaran');

                                    if ($metode !== 'transfer') return;
                                    if (!$akunId) return;

                                    $saldo = \App\Helpers\AkunHelper::getSaldo($akunId);

                                    if ($state > $saldo) {
                                        $set('biaya', $saldo);
                                    }
                                })
                                ->rule(function (Get $get) {
                                    return function ($attribute, $value, $fail) use ($get) {

                                        $akunId = $get('id_akun');
                                        $metode = $get('metode_pembayaran');

                                        // hanya validasi kalau transfer
                                        if ($metode !== 'transfer') return;

                                        if (!$akunId) return;

                                        $saldo = \App\Helpers\AkunHelper::getSaldo($akunId);

                                        if ($value > $saldo) {
                                            $fail('Saldo tidak cukup. Sisa: Rp ' . number_format($saldo, 0, ',', '.'));
                                        }
                                    };
                                })
                                 ->helperText(function (Get $get) {
                                    $akunId = $get('id_akun');

                                    if (!$akunId) return 'Pilih akun terlebih dahulu';

                                    $saldo = \App\Helpers\AkunHelper::getSaldo($akunId);

                                    return 'Saldo tersedia: Rp ' . number_format($saldo, 0, ',', '.');
                                })
                                ->maxLength(15),
                        ]),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Catatan tambahan (opsional)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
