<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Schemas;

use App\Filament\Support\MoneyInput;
use App\Models\coa;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaldoAwalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('coa_id')
                ->label('Akun (COA)')
                ->options(fn () => coa::orderBy('kode_akun')->get()->mapWithKeys(fn ($c) => [
                    $c->id => "[{$c->kode_akun}] {$c->nama_akun}",
                ]))
                ->searchable()
                ->required(),

            Select::make('bulan')
                ->label('Bulan')
                ->options([
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                    4 => 'April',   5 => 'Mei',       6 => 'Juni',
                    7 => 'Juli',    8 => 'Agustus',   9 => 'September',
                    10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                ])
                ->required(),

            TextInput::make('tahun')
                ->label('Tahun')
                ->numeric()
                ->minValue(2000)
                ->maxValue(2100)
                ->default(now()->year)
                ->required(),

            MoneyInput::make('nominal')
                ->label('Nominal (Rp)')
                ->required(),
        ]);
    }
}
