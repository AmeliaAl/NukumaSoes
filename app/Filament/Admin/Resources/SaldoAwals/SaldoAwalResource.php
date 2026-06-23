<?php

namespace App\Filament\Admin\Resources\SaldoAwals;

use App\Filament\Admin\Resources\SaldoAwals\Pages\CreateSaldoAwal;
use App\Filament\Admin\Resources\SaldoAwals\Pages\EditSaldoAwal;
use App\Filament\Admin\Resources\SaldoAwals\Pages\ListSaldoAwals;
use App\Models\SaldoAwal;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SaldoAwalResource extends Resource
{
    protected static ?string $model = SaldoAwal::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static UnitEnum|string|null $navigationGroup = 'Akuntansi';
    protected static ?string $navigationLabel = 'Saldo Awal';
    protected static ?string $pluralModelLabel = 'Saldo Awal';
    protected static ?string $modelLabel = 'Saldo Awal';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Admin\Resources\SaldoAwals\Schemas\SaldoAwalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Admin\Resources\SaldoAwals\Tables\SaldoAwalTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSaldoAwals::route('/'),
            'create' => CreateSaldoAwal::route('/create'),
            'edit'   => EditSaldoAwal::route('/{record}/edit'),
        ];
    }
}
