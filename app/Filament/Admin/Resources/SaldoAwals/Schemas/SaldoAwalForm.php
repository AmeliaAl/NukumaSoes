<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class SaldoAwalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bulan')
                    ->label('Bulan')
                    ->options([
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->required(),
                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->extraInputAttributes([
                        'oninput' => 'this.value = this.value.replace(/[^0-9]/g, "")',
                    ])
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->required(),
                Select::make('akun_id')
                    ->relationship('akun', 'nama_akun')
                    ->label('Nama Akun')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                TextInput::make('nominal')
                    ->label('Nominal')
                    ->numeric()
                    ->minValue(0)
                   ->extraInputAttributes([
                        'oninput' => 'this.value = this.value.replace(/[^0-9]/g, "")',
                    ])
                    ->prefix('Rp')
                    ->required(),
            ]);
    }
}
