<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaldoAwalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ])
                    ->required(),
                \Filament\Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric(),
                \Filament\Forms\Components\Select::make('coa_id')
                    ->relationship('coa', 'nama_akun')
                    ->required()
                    ->label('Nama Akun')
                    ->unique(
                        table: 'saldo_awals',
                        column: 'coa_id',
                        modifyRuleUsing: function ($rule, $get) {
                            return $rule->where('bulan', $get('bulan'))->where('tahun', $get('tahun'));
                        },
                        ignoreRecord: true
                    )
                    ->validationMessages([
                        'unique' => 'Nama Akun ini sudah memiliki saldo awal di periode tersebut.',
                    ]),
                \Filament\Forms\Components\TextInput::make('nominal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }
}
