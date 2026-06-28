<?php

namespace App\Filament\Admin\Resources\Mitras\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\Mitra; 

class MitraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_mitra')
                    ->default(fn () => Mitra::getKodeMitra()) // Ambil default dari method getKodeMitra
                    ->label('Kode Mitra')
                    ->required(),
                Select::make('jenisMitra')
                    ->label('Jenis Mitra')
                    ->options([
                        'agen' => 'Agen',
                        'reseller' => 'Reseller',
                        'konsinyasi' => 'Konsinyasi',
                        'makloon' => 'Makloon',
                    ])
                    ->placeholder('Pilih Jenis Mitra')
                    ->required(),
                TextInput::make('namaMitra')
                    ->placeholder('Masukkan nama Mitra')
                    ->required(),
                TextInput::make('alamat')
                    ->placeholder('Masukkan Alamat Mitra')
                    ->required(),
                TextInput::make('no_telepon')
                    ->placeholder('No Telepon')
                    ->tel()
                    ->numeric()
                    ->maxLength(12)
                    ->regex('/^[0-9]+$/')
                    ->required(),
                TextInput::make('limit_piutang')
                    ->label('Limit Piutang')
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp')
                    ->required(),
            ]);
    }
}
