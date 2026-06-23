<?php

namespace App\Filament\Admin\Resources\PerolehanAsets;

use App\Filament\Admin\Resources\PerolehanAsets\Pages\CreatePerolehanAset;
use App\Filament\Admin\Resources\PerolehanAsets\Pages\EditPerolehanAset;
use App\Filament\Admin\Resources\PerolehanAsets\Pages\ListPerolehanAsets;
use App\Filament\Admin\Resources\PerolehanAsets\Schemas\PerolehanAsetForm;
use App\Filament\Admin\Resources\PerolehanAsets\Tables\PerolehanAsetsTable;
use App\Models\PerolehanAset;
use App\Traits\HasRoleAccess;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Infolist;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;

class PerolehanAsetResource extends Resource
{
    use HasRoleAccess;

    protected static ?string $model = PerolehanAset::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Tambah Aset Tetap';
    protected static ?string $pluralModelLabel = 'Pembelian Aset Tetap';
     protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return PerolehanAsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PerolehanAsetsTable::configure($table);
    }

public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Transaksi')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('fakturPembelian.no_faktur')
                            ->label('No. Faktur')
                            ->copyable()
                            ->weight('bold')
                            ->color('primary'),

                        TextEntry::make('tanggal_faktur')
                            ->label('Tanggal Faktur')
                            ->date('d F Y'),

                        TextEntry::make('vendor.nama_vendor')
                            ->label('Vendor')
                            ->badge()
                            ->color('info'),
                    ]),

                Section::make('Informasi Aset')
                    ->icon('heroicon-o-cube')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nama_aset')
                            ->label('Nama Aset')
                            ->weight('bold')
                            ->size('lg')
                            ->color('success')
                            ->columnSpanFull(),

                        TextEntry::make('kategoriAset.nama_kategori')
                            ->label('Kategori Aset')
                            ->badge(),

                        TextEntry::make('tanggal_pakai')
                            ->label('Tanggal Mulai Pakai')
                            ->date('d F Y'),
                    ]),

                Section::make('Detail Biaya')
                    ->icon('heroicon-o-calculator')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('qty')
                            ->label('Quantity')
                            ->numeric()
                            ->suffix(' unit'),

                        TextEntry::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->money('IDR'),

                        TextEntry::make('biaya_lain')
                            ->label('Biaya Lain-lain')
                            ->money('IDR'),

                        TextEntry::make('total_perolehan')
                            ->label('Total Perolehan')
                            ->money('IDR')
                            ->weight('bold')
                            ->size('lg')
                            ->color('success')
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Penyusutan')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('masa_manfaat')
                            ->label('Umur Manfaat')
                            ->suffix(' tahun')
                            ->numeric(),

                        TextEntry::make('metode_penyusutan')
                            ->label('Metode Penyusutan')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('nilai_residu')
                            ->label('Nilai Residu')
                            ->money('IDR'),
                    ]),

                Section::make('Informasi Sistem')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d F Y, H:i'),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->dateTime('d F Y, H:i'),
                    ]),
            ]);
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
            'index' => ListPerolehanAsets::route('/'),
            'create' => CreatePerolehanAset::route('/create'),
            //'edit' => EditPerolehanAset::route('/{record}/edit'),
        ];
    }
}
