<?php

namespace App\Filament\Admin\Resources\Modals\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\JurnalDetail; // ✅ FIX ERROR
use App\Models\Akun;
use App\Helpers\AkunHelper;


class ModalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([

                DatePicker::make('tanggal')
                    ->required(),

                Select::make('jenis')
                    ->label('Jenis Transaksi')
                    ->options([
                        'setoran' => 'Setoran Pemilik',
                        'prive' => 'Prive - Penarikan Modal',
                    ])
                    ->required(),

                TextInput::make('jumlah')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->minValue(1)
                    ->live(onBlur: true)
                    ->rules([
                        function (Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {

                                $akunId = $get('id_akun');
                                $jenis  = $get('jenis');
                                $tanggal = $get('tanggal') ?? now();

                                // hanya validasi untuk prive
                                if ($jenis !== 'prive' || !$akunId) return;

                                $akun = Akun::find($akunId);
                                if (!$akun) return;

                                $saldoAwal = $akun->saldo ?? 0;

                                $debit = JurnalDetail::where('no_akun', $akunId)
                                    ->whereHas('jurnal', fn($q) => 
                                        $q->where('tanggal', '<=', $tanggal)
                                    )
                                    ->sum('debit');

                                $kredit = JurnalDetail::where('no_akun', $akunId)
                                    ->whereHas('jurnal', fn($q) => 
                                        $q->where('tanggal', '<=', $tanggal)
                                    )
                                    ->sum('credit');

                                $saldo = $saldoAwal + $debit - $kredit;

                                if ((float) $value > $saldo) {
                                    $fail('Jumlah melebihi saldo tersedia (Rp ' . number_format($saldo, 0, ',', '.') . ')');
                                }
                            };
                        }
                    ]),

                Select::make('id_akun')
                    ->label('Kas / Bank')
                    ->relationship(
                        'akun',
                        'nama_akun',
                        fn ($query) => $query->where('nama_akun', 'like', 'kas%')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->helperText(function (Get $get): string {
                        $akunId = $get('id_akun');
                        if (!$akunId) return 'Pilih kas/bank terlebih dahulu';

                        $saldo = AkunHelper::getSaldo($akunId);

                        return 'Saldo tersedia: Rp ' . number_format($saldo, 0, ',', '.');
                    })

                    ->rules([
                            function (Get $get) {
                                return function ($attribute, $value, $fail) use ($get) {

                                    $akunId  = $get('id_akun');
                                    $tanggal = $get('tanggal');
                                    $jenis   = $get('jenis');

                                    // ⛔ HANYA validasi kalau prive
                                    if ($jenis !== 'prive') return;

                                    if (!$akunId || !$tanggal) return;

                                    $saldo = AkunHelper::getSaldo($akunId, $tanggal);

                                    if ((float) $value > $saldo) {
                                        $fail('Jumlah melebihi saldo tersedia (Rp ' . number_format($saldo, 0, ',', '.') . ')');
                                    }
                                };
                            }
                        ])
            ]);
    }
}