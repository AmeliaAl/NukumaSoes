<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use App\Models\LaporanKonsinyasi;

class LaporanKonsinyasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('no_laporan')
                ->label('No Laporan')
                ->default(fn () => LaporanKonsinyasi::generateNo())
                ->disabled()
                ->dehydrated()
                ->required(),

            Select::make('penjualan_konsinyasi_id')
                ->label('No Konsinyasi')
                ->relationship(
                    'penjualanKonsinyasi',
                    'no_konsinyasi',
                    fn ($query) => $query->whereIn('status', ['BELUM TERJUAL', 'SEBAGIAN TERJUAL'])
                )
                ->searchable()
                ->required(),

            TextInput::make('no_po_mitra')
                ->label('No PO Mitra')
                ->required(),

            DatePicker::make('periode_awal')
                ->required(),

            DatePicker::make('periode_akhir')
                ->required(),
                
            TextInput::make('total_laporan')
                ->label('Total Tagihan')
                ->numeric()
                ->disabled()
                ->dehydrated()
                ->helperText('Otomatis dari total laporan'),
            Textarea::make('keterangan'),
        ]);
    }
}