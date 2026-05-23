<?php

namespace App\Filament\Admin\Resources\SalesOrders;

use App\Filament\Admin\Resources\SalesOrders\Pages\CreateSalesOrder;
use App\Filament\Admin\Resources\SalesOrders\Pages\EditSalesOrder;
use App\Filament\Admin\Resources\SalesOrders\Pages\ListSalesOrders;
use App\Filament\Admin\Resources\SalesOrders\Pages\ViewSalesOrder;
use App\Filament\Admin\Resources\SalesOrders\Schemas\SalesOrderForm;
use App\Filament\Admin\Resources\SalesOrders\Schemas\SalesOrderInfolist;
use App\Models\SalesOrder;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Tables\Actions\Action as TableAction;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static UnitEnum|string|null $navigationGroup = 'Gudang';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Sales Order';
    protected static ?string $recordTitleAttribute = 'no_so';
    protected static ?string $modelLabel = 'Sales Order';
    protected static ?string $pluralModelLabel = 'Sales Order';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return SalesOrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalesOrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_so')
                    ->label('No SO')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('referensi')
                    ->label('Referensi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Non Konsinyasi' => 'success',
                        'Konsinyasi' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('tanggal')
                            ->label('Filter Tanggal')
                            ->hiddenLabel()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tanggal'],
                            fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '=', $date),
                        );
                    })
            ])
            ->recordActions([
                Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn ($record) => route('sales-order.pdf', $record->id))
                    ->openUrlInNewTab()
                    ->color('info'),
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record])),
                DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Admin\Resources\SalesOrders\RelationManagers\DetailSalesOrderRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalesOrders::route('/'),
            // Create dan Edit dinonaktifkan karena SO dibuat otomatis oleh sistem
            // 'create' => CreateSalesOrder::route('/create'),
            'view' => ViewSalesOrder::route('/{record}'),
            // 'edit' => EditSalesOrder::route('/{record}/edit'),
        ];
    }
}
