<?php

namespace App\Filament\Admin\Resources\Jurnals\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use App\Models\Akun;


class JurnalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make('Deskripsi Jurnal')
            ->columnSpanFull()
                ->schema([
                    DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->default(now())
                        ->required(),

                    TextInput::make('no_referensi')
                        ->label('No Referensi')
                        ->maxLength(100),

                    Textarea::make('deskripsi')
                        ->label('Deskripsi'),
                ])
                ->columns(3)
                ->collapsible(),

            Section::make('Detail Jurnal')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('jurnaldetail')
                        ->relationship('jurnaldetail')
                        ->label('Detail Jurnal')
                        ->schema([
                            Select::make('no_akun')
                                ->label('Akun')
                                ->options(Akun::pluck('nama_akun', 'id'))
                                ->searchable()
                                ->required(),

                    TextInput::make('debit')
                                ->numeric()
                                ->default(0)
                                ->prefix('Rp ')
                                ->required()
                                ->label('Debit'),

                    TextInput::make('credit')
                                ->numeric()
                                ->default(0)
                                ->prefix('Rp ')
                                ->required()
                                ->label('Kredit'),

                    Textarea::make('deskripsi')
                                ->rows(2)
                                ->label('Keterangan'),
                        ])
                        ->columns(2)
                        ->minItems(1),
                ])
                ->collapsible(),
            ]);
    }
}
