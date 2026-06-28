<?php

namespace App\Filament\Admin\Resources\DetailPersediaanProduks;

use App\Models\DetailPersediaanProduk;
use App\Filament\Admin\Resources\DetailPersediaanProduks\Pages\CreateDetailPersediaanProduk;
use App\Filament\Admin\Resources\DetailPersediaanProduks\Pages\EditDetailPersediaanProduk;
use App\Filament\Admin\Resources\DetailPersediaanProduks\Pages\ListDetailPersediaanProduks;
use App\Filament\Admin\Resources\DetailPersediaanProduks\Schemas\DetailPersediaanProdukForm;
use App\Filament\Admin\Resources\DetailPersediaanProduks\Tables\DetailPersediaanProduksTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use Illuminate\Support\Facades\Auth;

class DetailPersediaanProdukResource extends Resource
{
    protected static ?string $model = DetailPersediaanProduk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;
    protected static ?string $navigationLabel = 'Persediaan Produk';
    protected static UnitEnum|string|null $navigationGroup = 'Gudang';
    protected static ?string $pluralModelLabel = 'Detail Persediaan Produk';
    protected static ?string $modelLabel = 'Persediaan Produk';

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
        return DetailPersediaanProdukForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DetailPersediaanProduksTable::configure($table);
    }

    /**
     * Default query menggunakan urutan FEFO (First Expired First Out):
     * batch dengan tanggal_expired paling dekat ditampilkan lebih dulu.
     * Batch tanpa tanggal expired diletakkan paling akhir.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('barang.kategori')
            ->orderByRaw('tanggal_expired IS NULL ASC')
            ->orderBy('tanggal_expired', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDetailPersediaanProduks::route('/'),
            'create' => CreateDetailPersediaanProduk::route('/create'),
            'edit'   => EditDetailPersediaanProduk::route('/{record}/edit'),
        ];
    }
}
