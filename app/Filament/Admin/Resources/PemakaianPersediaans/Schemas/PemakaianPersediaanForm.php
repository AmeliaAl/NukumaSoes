<?php

namespace App\Filament\Admin\Resources\PemakaianPersediaans\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Models\AsetLancar;

class PemakaianPersediaanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
            Select::make('aset_lancar_id')
                ->label('Nama Barang')
                ->options(
                    AsetLancar::query()
                        ->pluck('nama_barang', 'id')
                        ->toArray()
                )
                ->searchable()
                ->required(),

            DatePicker::make('tanggal')
                ->label('Tanggal Pemakaian')
                ->required()
                ->maxDate(now())
                ->default(now()),

            TextInput::make('jumlah')
                ->label('Jumlah Dipakai')
                ->numeric()
                ->required()
                ->minValue(1),

            Textarea::make('keterangan')
                ->label('Keterangan')
                ->nullable(),
            ]);
    }
}
