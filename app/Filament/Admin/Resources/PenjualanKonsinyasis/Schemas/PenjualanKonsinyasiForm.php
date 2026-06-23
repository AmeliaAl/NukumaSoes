<?php

namespace App\Filament\Admin\Resources\PenjualanKonsinyasis\Schemas;

use App\Filament\Support\MoneyInput;
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
                                ->maxDate(now())
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
                                ->required()
                                ->live()
                                ->rule(function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $mitra = Mitra::where('kode_mitra', $value)->first();
                                        if ($mitra && $mitra->sisa_limit_piutang <= 0) {
                                            $fail("Limit piutang mitra telah melebihi batas. Sisa limit: Rp " . number_format($mitra->sisa_limit_piutang, 0, ',', '.'));
                                        }
                                    };
                                }),

                            Placeholder::make('info_limit_piutang')
                                ->label('Informasi Limit Piutang')
                                ->content(function (Get $get) {
                                    $kode_mitra = $get('kode_mitra');
                                    if (! $kode_mitra) {
                                        return 'Pilih mitra terlebih dahulu';
                                    }

                                    $mitra = Mitra::where('kode_mitra', $kode_mitra)->first();
                                    if (! $mitra) {
                                        return '-';
                                    }

                                    $limit = $mitra->limit_piutang;
                                    $aktif = $mitra->piutang_aktif_konsinyasi;
                                    $sisa = $mitra->sisa_limit_piutang;

                                    $color = 'green';
                                    if ($sisa <= 0) {
                                        $color = 'red';
                                    } elseif ($sisa <= ($limit * 0.2)) {
                                        $color = 'orange';
                                    }

                                    return new \Illuminate\Support\HtmlString("
                                        <div style='display: flex; flex-direction: column; gap: 4px;'>
                                            <div>Limit Piutang: <strong>Rp " . number_format($limit, 0, ',', '.') . "</strong></div>
                                            <div>Total Utang Aktif: <strong>Rp " . number_format($aktif, 0, ',', '.') . "</strong></div>
                                            <div style='color: {$color}; font-weight: bold;'>Sisa Limit: Rp " . number_format($sisa, 0, ',', '.') . "</div>
                                        </div>
                                    ");
                                })
                                ->columnSpanFull(),

                            TextInput::make('term')
                                ->label('Term (Hari)')
                                ->numeric()
                                ->default(30)
                                ->minValue(1)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::hitungJatuhTempo($get, $set);
                                }),

                            DatePicker::make('jatuh_tempo')
                                ->label('Jatuh Tempo')
                                ->disabled()
                                ->dehydrated()
                                ->validationMessages([
                                    'required' => 'Kolom Jatuh Tempo wajib diisi.',
                                ])
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
                            MoneyInput::make('diskon')
                                ->label('Diskon Faktur')
                                ->default(0)
                                ->helperText('Masukkan diskon tambahan (jika ada).')
                                ->live(debounce: 500),

                            Placeholder::make('total_diskon')
                                ->label('Total Diskon (Faktur + Barang)')
                                ->content(function (Get $get, $livewire) {
                                    $diskonFaktur = (int) str_replace('.', '', $get('diskon') ?? '0');
                                    $diskonDetail = 0;
                                    if (method_exists($livewire, 'getRecord') && $livewire->getRecord()) {
                                        $diskonDetail = (int) $livewire->getRecord()->detailKonsinyasi()->sum('diskon');
                                    }
                                    $totalDiskon = $diskonFaktur + $diskonDetail;
                                    
                                    return 'Rp ' . number_format($totalDiskon, 0, ',', '.');
                                }),

                            Placeholder::make('total_preview')
                            ->label('Total (Setelah Diskon)')
                            ->content(function (Get $get, $livewire) {
                                $subtotal = method_exists($livewire, 'getSubtotalProperty')
                                    ? $livewire->getSubtotalProperty()
                                    : 0;

                                if ($subtotal <= 0) {
                                    return 'Total akan muncul setelah barang ditambahkan';
                                }

                                $diskon = (int) str_replace('.', '', $get('diskon') ?? '0');
                                $total = max($subtotal - $diskon, 0);

                                return 'Rp ' . number_format($total, 0, ',', '.');
                            }),

                        ])
                        ->columns(4)
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