<?php

namespace App\Filament\Admin\Resources\LaporanKonsinyasis;

use App\Filament\Admin\Resources\LaporanKonsinyasis\Pages\CreateLaporanKonsinyasi;
use App\Filament\Admin\Resources\LaporanKonsinyasis\Pages\EditLaporanKonsinyasi;
use App\Filament\Admin\Resources\LaporanKonsinyasis\Pages\ListLaporanKonsinyasis;
use App\Filament\Admin\Resources\LaporanKonsinyasis\Pages\ViewLaporanKonsinyasi;
use App\Filament\Admin\Resources\LaporanKonsinyasis\Schemas\LaporanKonsinyasiForm;
use App\Filament\Admin\Resources\LaporanKonsinyasis\Schemas\LaporanKonsinyasiInfolist;
use App\Filament\Admin\Resources\LaporanKonsinyasis\Tables\LaporanKonsinyasisTable;
use App\Filament\Admin\Resources\LaporanKonsinyasis\RelationManagers\DetailLaporanRelationManager;
use App\Models\LaporanKonsinyasi;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LaporanKonsinyasiResource extends Resource
{
    protected static ?string $model = LaporanKonsinyasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;
    protected static UnitEnum|string|null $navigationGroup = 'Penjualan Konsinyasi';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'no_laporan';
    protected static ?string $navigationLabel = 'Laporan Penjualan Konsinyasi';
    protected static ?string $modelLabel = 'Laporan Penjualan Konsinyasi';
    protected static ?string $pluralModelLabel = 'Rekap Laporan Penjualan Konsinyasi dari mitra';
    
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

         return $user->isPenjualans() || $user->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return LaporanKonsinyasiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LaporanKonsinyasiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaporanKonsinyasisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DetailLaporanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaporanKonsinyasis::route('/'),
            'create' => CreateLaporanKonsinyasi::route('/create'),
            'view' => ViewLaporanKonsinyasi::route('/{record}'),
            'edit' => EditLaporanKonsinyasi::route('/{record}/edit'),
        ];
    }
}
