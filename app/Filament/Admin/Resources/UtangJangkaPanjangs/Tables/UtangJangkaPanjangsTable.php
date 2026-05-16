<?php

namespace App\Filament\Admin\Resources\UtangJangkaPanjangs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\UtangJangkaPanjang;
use App\Models\PembayaranUtangJangkaPanjang;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

class UtangJangkaPanjangsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama_utang')
                    ->label('Nama Utang')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold'),

                TextColumn::make('akunKredit.nama_akun')
                    ->label('Akun Utang')
                    ->sortable()
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold'),

                TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->jatuh_tempo < now() ? 'danger' : 'success')
                    ->weight(fn ($record) => $record->jatuh_tempo < now() ? 'bold' : 'normal')
                    ->badge()
                    ->icon(fn ($record) => $record->jatuh_tempo < now() ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'),

                TextColumn::make('akunDebit.nama_akun')
                    ->label('Akun Debit')
                    ->sortable()
                    ->toggleable()
                    ->wrap(),

                TextColumn::make('status_lunas')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->sisa_utang <= 0 ? 'Lunas' : 'Belum Lunas')
                    ->badge()
                    ->color(fn ($record) => $record->sisa_utang <= 0 ? 'success' : 'danger'),

                TextColumn::make('total_dibayar')
                    ->label('Sudah Dibayar')
                    ->getStateUsing(fn ($record) => $record->pembayaran()->sum('nominal'))
                    ->money('IDR'),

                TextColumn::make('sisa_utang')
                    ->label('Sisa Utang')
                    ->getStateUsing(fn ($record) => $record->sisa_utang)
                    ->money('IDR'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])

            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['dari'],
                                fn ($q) => $q->whereDate('tanggal', '>=', $data['dari'])
                            )
                            ->when(
                                $data['sampai'],
                                fn ($q) => $q->whereDate('tanggal', '<=', $data['sampai'])
                            );
                    }),

                Filter::make('jatuh_tempo_lewat')
                    ->label('Sudah Jatuh Tempo')
                    ->query(fn (Builder $query) => $query->where('jatuh_tempo', '<', now()))
                    ->toggle(),
            ])

            ->recordActions([
                ViewAction::make(),
               // EditAction::make(),
               Action::make('pelunasan')
                ->label('Pelunasan')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->visible(fn ($record) => $record->sisa_utang > 0)
                ->form([
                    DatePicker::make('tanggal_bayar')
                        ->minDate(fn ($record) => $record->tanggal)
                        ->label('Tanggal Bayar')
                        ->required(),

                    TextInput::make('nominal')
                        ->label('Nominal Bayar')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(1)
                        ->maxValue(fn ($record) => $record->sisa_utang)
                        ->required(),

                    Select::make('akun_id')
                        ->label('Akun Kas/Bank')
                        ->options(
                            Akun::query()
                                ->where('nama_akun', 'like', '%kas%')
                                ->orWhere('nama_akun', 'like', '%bank%')
                                ->pluck('nama_akun', 'id')
                        )
                        ->searchable()
                        ->required(),
                ])

                ->action(function ($record, array $data) {
                    $nominalBayar = (float) $data['nominal'];

                    $saldoKas = \App\Models\JurnalDetail::where('no_akun', $data['akun_id'])
                            ->whereHas('jurnal', function ($q) use ($data) {
                                $q->whereDate('tanggal', '<=', $data['tanggal_bayar']);
                            })
                            ->sum(\DB::raw('debit - credit'));

                    if ($nominalBayar > $record->sisa_utang) {
                            Notification::make()
                                ->title('Nominal terlalu besar')
                                ->body('Nominal bayar tidak boleh lebih besar dari sisa utang.')
                                ->danger()
                                ->send();

                            throw new Halt();
                    }

                    if ($nominalBayar > $saldoKas) {
                        Notification::make()
                            ->title('Saldo tidak cukup')
                            ->body('Saldo tersedia Rp ' . number_format($saldoKas, 0, ',', '.'))
                            ->danger()
                            ->send();

                        throw new Halt();
                    }

                    $record->pembayaran()->create([
                        'tanggal_bayar' => $data['tanggal_bayar'],
                        'nominal' => $nominalBayar,
                        'akun_id' => $data['akun_id'],
                    ]);

                    // bikin jurnal
                    $jurnal = Jurnal::create([
                        'tanggal' => $data['tanggal_bayar'],
                        'keterangan' => 'Pelunasan utang ' . $record->nama_utang,
                    ]);

                    // detail jurnal
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id,
                        'no_akun' => $record->akun_id, // akun utang
                        'debit' => $nominalBayar,
                        'credit' => 0,
                    ]);

                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id,
                        'no_akun' => $data['akun_id'], // kas/bank
                        'debit' => 0,
                        'credit' => $nominalBayar,
                    ]);
                }),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }
}
