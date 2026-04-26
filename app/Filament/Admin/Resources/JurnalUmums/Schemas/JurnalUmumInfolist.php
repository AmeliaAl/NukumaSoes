<?php

namespace App\Filament\Admin\Resources\JurnalUmums\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;

class JurnalUmumInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([

                TextEntry::make('tanggal')
                    ->label('Tanggal Transaksi')
                    ->date(),

                TextEntry::make('no_jurnal')
                    ->label('Nomor Jurnal')
                    ->badge(),

                TextEntry::make('keterangan')
                    ->label('Referensi')
                    ->columnSpan(3),

            ]),
        ]);
    }
}
