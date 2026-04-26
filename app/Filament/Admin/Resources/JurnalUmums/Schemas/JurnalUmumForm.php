<?php

namespace App\Filament\Admin\Resources\JurnalUmums\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JurnalUmumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->required(),
                TextInput::make('keterangan')
                    ->default(null),
                TextInput::make('ref_type')
                    ->default(null),
                TextInput::make('ref_id')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
