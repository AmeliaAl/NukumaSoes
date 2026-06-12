<?php

namespace App\Filament\Admin\Resources\SaldoAwals;

use App\Filament\Admin\Resources\SaldoAwals\Pages\CreateSaldoAwal;
use App\Filament\Admin\Resources\SaldoAwals\Pages\EditSaldoAwal;
use App\Filament\Admin\Resources\SaldoAwals\Pages\ListSaldoAwals;
use App\Filament\Admin\Resources\SaldoAwals\Schemas\SaldoAwalForm;
use App\Filament\Admin\Resources\SaldoAwals\Tables\SaldoAwalsTable;
use App\Models\SaldoAwal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SaldoAwalResource extends Resource
{
    protected static ?string $model = SaldoAwal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SaldoAwalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SaldoAwalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSaldoAwals::route('/'),
            'create' => CreateSaldoAwal::route('/create'),
            'edit' => EditSaldoAwal::route('/{record}/edit'),
        ];
    }
}
