<?php

namespace App\Filament\Admin\Resources\SalesOrders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SalesOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('no_so')
                ->label('No SO'),

            TextEntry::make('tanggal')
                ->label('Tanggal')
                ->date('d/m/Y'),

            TextEntry::make('referensi')
                ->label('Referensi'),

            TextEntry::make('jenis')
                ->label('Jenis')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Non Konsinyasi' => 'success',
                    'Konsinyasi' => 'info',
                    default => 'gray',
                }),

            TextEntry::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Draft' => 'gray',
                    'Diproses' => 'warning',
                    'Selesai' => 'success',
                    default => 'gray',
                }),
        ]);
    }
}
