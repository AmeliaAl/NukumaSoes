<?php

namespace App\Filament\Admin\Resources\SaldoAwals\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Akun;

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
                    ->placeholder('yyyy')
                    ->numeric()
                    ->extraInputAttributes([
                        'oninput' => 'this.value = this.value.replace(/[^0-9]/g, "")',
                        'maxlength' => 4,
                    ])
                    ->minValue(now()->year - 10)
                    ->maxValue(now()->year)
                    ->required(),
                Select::make('akun_id')
                    ->relationship(
                        'akun',
                        'nama_akun',
                        fn ($query) => $query->whereIn('header_akun', [1, 2, 3])
                    )
                    ->label('Nama Akun')
                    ->helperText('Hanya akun Aset, Kewajiban, dan Ekuitas yang dapat diinput')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required()
                    ->unique(
                        table: 'saldoawal',
                        column: 'akun_id',
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule, $get) {
                            return $rule
                                ->where('bulan', $get('bulan'))
                                ->where('tahun', $get('tahun'));
                        }
                    )
                    ->validationMessages([
                        'unique' => 'Saldo awal untuk akun ini pada bulan dan tahun yang sama sudah ada.',
                    ]),
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
