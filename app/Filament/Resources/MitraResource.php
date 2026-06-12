<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MitraResource\Pages;
use App\Models\Mitra;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MitraResource extends Resource
{
    protected static ?string $model = Mitra::class;

    protected static ?string $modelLabel = 'Mitra';
    protected static ?string $pluralModelLabel = 'Mitra';
    protected static ?string $navigationLabel = 'Mitra';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-users';
    }

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nama_mitra')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jenis_mitra')
                    ->maxLength(255),
                Forms\Components\Textarea::make('alamat')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('no_hp')
                    ->label('No. HP')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Section::make('Daftar Harga Khusus Mitra')
                    ->schema([
                        Forms\Components\TextInput::make('harga')
                            ->label('Harga Produk Umum')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('soes_kemasan_pouch_trendy')
                            ->label('Harga Pouch Trendy')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('soes_kemasan_toples_ecofam')
                            ->label('Harga Toples Ecofam')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('soes_kemasan_toples_family')
                            ->label('Harga Toples Family')
                            ->numeric()
                            ->prefix('Rp'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_mitra')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_mitra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMitras::route('/'),
            'create' => Pages\CreateMitra::route('/create'),
            'edit' => Pages\EditMitra::route('/{record}/edit'),
        ];
    }
}
