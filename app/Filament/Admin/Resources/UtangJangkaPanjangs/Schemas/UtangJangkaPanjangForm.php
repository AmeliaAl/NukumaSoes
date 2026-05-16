<?php

namespace App\Filament\Admin\Resources\UtangJangkaPanjangs\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use App\Models\Akun;

class UtangJangkaPanjangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utang')
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        TextInput::make('nama_utang')
                            ->label('Nama Utang')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Pinjaman Bank BCA'),

                        TextInput::make('nominal')
                            ->label('Nominal')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(1)
                            ->rules(['gt:0'])
                            ->helperText('Nominal harus lebih besar dari 0'),

                        DatePicker::make('jatuh_tempo')
                            ->label('Jatuh Tempo')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('tanggal')
                            ->helperText('Tanggal jatuh tempo harus setelah tanggal utang'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Akun Terkait')
                    ->columnSpanFull()
                    ->description('Pilih akun untuk pencatatan jurnal otomatis')
                    ->schema([
                        Select::make('akun_debit_id')
                            ->label('Akun Debit (Kas/Aset yang Diterima)')
                            ->options(function () {
                                return Akun::where('header_akun', 1) // Aktiva
                                    ->pluck('nama_akun', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->helperText('Pilih Kas jika menerima uang tunai, atau Aset jika untuk pembelian aset')
                            ->preload(),

                        Select::make('akun_id')
                            ->label('Akun Kredit (Utang Jangka Panjang)')
                            ->options(function () {
                                return Akun::where('header_akun', 2) // Liabilitas
                                    ->where('no_akun', 'like', '2%') // Kode utang jangka panjang
                                    ->pluck('nama_akun', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->helperText('Pilih akun Utang Jangka Panjang dari kategori Liabilitas')
                            ->preload(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Tambahkan catatan atau keterangan tambahan (opsional)'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
