<?php

namespace App\Filament\Admin\Resources\PembayaranAsets;

use App\Filament\Admin\Resources\PembayaranAsets\Pages\CreatePembayaranAset;
use App\Filament\Admin\Resources\PembayaranAsets\Pages\EditPembayaranAset;
use App\Filament\Admin\Resources\PembayaranAsets\Pages\ListPembayaranAsets;
use App\Filament\Admin\Resources\PembayaranAsets\Schemas\PembayaranAsetForm;
use App\Filament\Admin\Resources\PembayaranAsets\Tables\PembayaranAsetsTable;
use App\Models\PembayaranAset;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PembayaranAsetResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = PembayaranAset::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Pembayaran Aset';
    protected static ?string $pluralModelLabel = 'Pembayaran Aset';
    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return PembayaranAsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PembayaranAsetsTable::configure($table);
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
            'index' => ListPembayaranAsets::route('/'),
            'create' => CreatePembayaranAset::route('/create'),
        ];
    }
}
