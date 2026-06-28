<?php

namespace App\Filament\Admin\Resources\FakturPembelians;

use App\Filament\Admin\Resources\FakturPembelians\Pages\CreateFakturPembelian;
use App\Filament\Admin\Resources\FakturPembelians\Pages\EditFakturPembelian;
use App\Filament\Admin\Resources\FakturPembelians\Pages\ListFakturPembelians;
use App\Filament\Admin\Resources\FakturPembelians\Schemas\FakturPembelianForm;
use App\Filament\Admin\Resources\FakturPembelians\Tables\FakturPembeliansTable;
use App\Models\FakturPembelian;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class FakturPembelianResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = FakturPembelian::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Faktur Pembelian';
    protected static ?string $pluralModelLabel = 'Faktur Pembelian Barang';
    protected static ?int $navigationSort = 10;

     public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

         return $user->isAsset() || $user->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return FakturPembelianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FakturPembeliansTable::configure($table);
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
            'index' => ListFakturPembelians::route('/'),
            'create' => CreateFakturPembelian::route('/create'),
            'edit' => EditFakturPembelian::route('/{record}/edit'),
        ];
    }

    // Tambahkan method ini untuk handle sebelum save
protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['total_tagihan'] = self::calculateTotalTagihan($data);
    return $data;
}

protected function mutateFormDataBeforeSave(array $data): array
{
    $data['total_tagihan'] = self::calculateTotalTagihan($data);
    return $data;
}

protected function calculateTotalTagihan(array $data): float
    {
        $subtotal = 0;
        
        // Hitung dari items yang ada
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $subtotal += (float) ($item['total_harga'] ?? 0);
            }
        }
        
        // Tambah biaya lain
        $biayaLain = (float) ($data['biaya_lain'] ?? 0);
        
        return $subtotal + $biayaLain;
    }


}
