<?php

namespace App\Filament\Admin\Resources\TagihanKonsinyasis\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;

class PembayaranTagihanRelationManager extends RelationManager
{
    protected static string $relationship = 'pembayaranTagihan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('tanggal_bayar')
                ->label('Tanggal Bayar')
                ->default(now())
                ->minDate(fn () => $this->ownerRecord->tanggal_tagihan ?? now())
                ->required(),

            TextInput::make('jumlah_bayar')
                ->label('Jumlah Bayar')
                ->numeric()
                ->required()
                ->minValue(1)
                ->maxValue(function () {
                    $tagihan = $this->ownerRecord;
                    return max((int) $tagihan->total_tagihan - (int) $tagihan->total_terbayar, 0);
                })
                ->live(debounce: 500)
                ->helperText(function (Get $get) {
                    $tagihan = $this->ownerRecord;
                    $sisa = (int) $tagihan->total_tagihan - (int) $tagihan->total_terbayar;
                    $jumlah = (int) ($get('jumlah_bayar') ?? 0);

                    if ($jumlah > $sisa) {
                        return new HtmlString(
                            '<span style="color:#dc2626; font-weight:500;">Jumlah bayar melebihi sisa tagihan. Maksimal pembayaran: Rp '
                            . number_format($sisa, 0, ',', '.')
                            . '</span>'
                        );
                    }

                    return 'Maksimal pembayaran: Rp ' . number_format($sisa, 0, ',', '.');
                }),

            Select::make('metode_bayar')
                ->label('Metode Bayar')
                ->options([
                    'transfer' => 'Transfer',
                    'tunai' => 'Tunai',
                ])
                ->required(),

            FileUpload::make('bukti_bayar')
                ->label('Bukti Bayar')
                ->directory('bukti-tagihan')
                ->image()
                ->maxSize(2048),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->label('Tanggal')
                    ->date(),

                Tables\Columns\TextColumn::make('jumlah_bayar')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id_ID'),

                Tables\Columns\TextColumn::make('metode_bayar')
                    ->label('Metode'),

                Tables\Columns\ImageColumn::make('bukti_bayar')
                    ->label('Bukti Bayar')
                    ->circular(false),
            ])
            ->headerActions([
                Action::make('tambah')
                    ->label('Tambah Pembayaran')
                    ->icon('heroicon-o-plus')
                    ->visible(fn () => ! $this->isReadOnly())
                    ->disabled(fn () => $this->ownerRecord->status === 'LUNAS')
                    ->tooltip(fn () => $this->ownerRecord->status === 'LUNAS' ? 'Tagihan sudah lunas' : null)
                    ->form(fn (Schema $schema) => $this->form($schema))
                    ->action(function (array $data) {
                        $tagihan = $this->ownerRecord->refresh();

                        $sisa = (int) $tagihan->total_tagihan - (int) $tagihan->total_terbayar;

                        if ((int) $data['jumlah_bayar'] <= 0) {
                            return;
                        }

                        if ((int) $data['jumlah_bayar'] > $sisa) {
                            return;
                        }

                        $this->getRelationship()->create($data);

                        $this->ownerRecord->refreshStatus();
                        $this->ownerRecord->refresh();
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->schema($this->viewSchema()),
                DeleteAction::make()
                    ->after(function () {
                        $this->ownerRecord->refreshStatus();
                        $this->ownerRecord->refresh();
                    }),
            ]);
    }

    protected function viewSchema(): array
    {
        return [
            Grid::make(2)->schema([
                Grid::make(1)->schema([
                    Placeholder::make('tanggal_bayar_view')
                        ->label('Tanggal Bayar')
                        ->content(fn ($record) => \Carbon\Carbon::parse($record->tanggal_bayar)->format('d/m/Y')),

                    Placeholder::make('jumlah_bayar_view')
                        ->label('Jumlah Bayar')
                        ->content(fn ($record) => 'Rp ' . number_format((int) $record->jumlah_bayar, 0, ',', '.')),

                    Placeholder::make('metode_bayar_view')
                        ->label('Metode Bayar')
                        ->content(fn ($record) => ucfirst($record->metode_bayar)),
                ]),

                FileUpload::make('bukti_bayar')
                    ->label('Bukti Bayar')
                    ->disabled(),
            ]),
        ];
    }
}