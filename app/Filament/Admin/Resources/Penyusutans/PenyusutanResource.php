<?php

namespace App\Filament\Admin\Resources\Penyusutans;

use App\Filament\Admin\Resources\Penyusutans\Pages\CreatePenyusutan;
use App\Filament\Admin\Resources\Penyusutans\Pages\EditPenyusutan;
use App\Filament\Admin\Resources\Penyusutans\Pages\ListPenyusutans;
use App\Filament\Admin\Resources\Penyusutans\Schemas\PenyusutanForm;
use App\Filament\Admin\Resources\Penyusutans\Tables\PenyusutansTable;
use App\Models\Penyusutan;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenyusutanResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = Penyusutan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Kartu Penyusutan Aset';
    protected static ?string $pluralModelLabel = 'Kartu Penyusutan Aset';
    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return PenyusutanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenyusutansTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(
                request()->get('tableFilters')['aset_id']['value'] ?? null,
                fn ($query, $asetId) =>
                    $query->where('aset_id', $asetId)
            );
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
            'index' => ListPenyusutans::route('/'),
            'create' => CreatePenyusutan::route('/create'),
        ];
    }


}
