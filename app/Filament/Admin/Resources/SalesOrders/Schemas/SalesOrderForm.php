<?php

namespace App\Filament\Admin\Resources\SalesOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SalesOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('no_so')
                ->label('No SO')
                ->default(fn () => \App\Models\SalesOrder::generateNoSO())
                ->disabled()
                ->dehydrated()
                ->required(),

            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(now())
                ->required(),

            TextInput::make('referensi')
                ->label('Referensi (No Invoice / No Konsinyasi)')
                ->required()
                ->maxLength(255),

            Select::make('jenis')
                ->label('Jenis')
                ->options([
                    'Non Konsinyasi' => 'Non Konsinyasi',
                    'Konsinyasi' => 'Konsinyasi',
                ])
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'Draft' => 'Draft',
                    'Diproses' => 'Diproses',
                    'Selesai' => 'Selesai',
                ])
                ->default('Draft')
                ->required(),
        ]);
    }
}
