<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\Schemas;

use App\Models\PenjualanKonsinyasi;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\Mitra;

class PenjualanKonsinyasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(1)
                ->schema([
                    Section::make('Data Konsinyasi')
                        ->schema([
                            TextInput::make('no_konsinyasi')
                                ->label('No Konsinyasi')
                                ->default(fn () => PenjualanKonsinyasi::generateNo())
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            DatePicker::make('tanggal')
                                ->label('Tanggal')
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::hitungJatuhTempo($get, $set);
                                }),

                            Select::make('kode_mitra')
                                ->label('Mitra')
                                ->options(function () {
                                    return Mitra::query()
                                        ->orderBy('namaMitra')
                                        ->get()
                                        ->mapWithKeys(function ($mitra) {
                                            return [
                                                $mitra->kode_mitra => "{$mitra->namaMitra} — " . ucfirst($mitra->jenisMitra),
                                            ];
                                        })
                                        ->toArray();
                                })
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search) {
                                    return Mitra::query()
                                        ->where('namaMitra', 'like', "%{$search}%")
                                        ->orWhere('jenisMitra', 'like', "%{$search}%")
                                        ->orWhere('kode_mitra', 'like', "%{$search}%")
                                        ->orderBy('namaMitra')
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function ($mitra) {
                                            return [
                                                $mitra->kode_mitra => "{$mitra->namaMitra} — " . ucfirst($mitra->jenisMitra),
                                            ];
                                        })
                                        ->toArray();
                                })
                                ->getOptionLabelUsing(function ($value): ?string {
                                    $mitra = Mitra::where('kode_mitra', $value)->first();

                                    return $mitra
                                        ? "{$mitra->namaMitra} — " . ucfirst($mitra->jenisMitra)
                                        : null;
                                })
                                ->required(),

                            TextInput::make('term')
                                ->label('Term (Hari)')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::hitungJatuhTempo($get, $set);
                                }),

                            DatePicker::make('jatuh_tempo')
                                ->label('Jatuh Tempo')
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            DatePicker::make('tanggal_kirim')
                                ->label('Tanggal Kirim')
                                ->nullable(),

                            Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),

                    Section::make('Ringkasan Faktur')
                        ->schema([
                            Placeholder::make('subtotal_preview')
                            ->label('Subtotal')
                            ->content(function ($livewire) {
                                $subtotal = method_exists($livewire, 'getSubtotalProperty')
                                    ? $livewire->getSubtotalProperty()
                                    : 0;

                                if ($subtotal <= 0) {
                                    return '⚠️ Belum ada barang ditambahkan';
                                }

                                return 'Rp ' . number_format($subtotal, 0, ',', '.');
                            }),
                            TextInput::make('diskon')
                                ->label('Diskon')
                                ->numeric()
                                ->default(0)
                                ->live(debounce: 500),

                            Placeholder::make('total_preview')
                            ->label('Total')
                            ->content(function (Get $get, $livewire) {
                                $subtotal = method_exists($livewire, 'getSubtotalProperty')
                                    ? $livewire->getSubtotalProperty()
                                    : 0;

                                if ($subtotal <= 0) {
                                    return 'Total akan muncul setelah barang ditambahkan';
                                }

                                $diskon = (int) ($get('diskon') ?? 0);
                                $total = max($subtotal - $diskon, 0);

                                return 'Rp ' . number_format($total, 0, ',', '.');
                            }),

                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }

    protected static function hitungJatuhTempo(Get $get, Set $set): void
    {
        $tanggal = $get('tanggal');
        $term = (int) $get('term');

        if ($tanggal && $term > 0) {
            $set(
                'jatuh_tempo',
                Carbon::parse($tanggal)->addDays($term)->toDateString()
            );
        }
    }
}