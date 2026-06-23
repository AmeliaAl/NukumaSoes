<?php

namespace App\Filament\Admin\Resources\Asets\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use App\Models\Aset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Set;
use App\Models\kategoriAset;


class AsetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kode_aset')
                ->label('Kode Aset')
                ->default(fn () => Aset::generateKdAset()) // Ambil default dari method generatekdaset
                ->readonly(),  

                TextInput::make('nama_aset')
                    ->required()
                    ->label('Nama Aset')
                    ->required()
                    ->placeholder('Masukkan nama aset'),
                    
                select::make('id_kategori')
                    ->required()
                    ->label('Kategori Aset')
                    ->relationship('kategori_aset', 'nama_kategori')
                    // 1. Mengaktifkan reaktivitas (agar field lain bisa merespons perubahan)
                    ->live() 
                    // 2. Aksi setelah state/nilai dari Select ini diperbarui
                    ->afterStateUpdated(function ($set, $state) {
                        // Cek jika state (ID Kategori) ada
                        if ($state) {
                            // Ambil model KategoriAset berdasarkan ID ($state)
                            $kategori = \App\Models\KategoriAset::find($state);
                            
                            // Set nilai 'masa_manfaat' pada field yang lain
                            if ($kategori) {
                                // Asumsi field di tabel kategori aset adalah 'masa_manfaat'
                                $set('masa_manfaat', $kategori->masa_manfaat); 
                            }
                        }
                    })
                    ->placeholder('Pilih Kategori Aset')
                    ->placeholder('Masukkan kategori aset'),

                DatePicker::make('tanggal_perolehan')
                    ->required()
                    ->label('Tanggal Perolehan Aset')
                    ->maxDate(now())
                    ->displayFormat('d/m/Y') 
                    ->placeholder('Pilih tanggal perolehan'),

                TextInput::make('nilai_perolehan')
                        ->label('Nilai Perolehan')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->prefix('Rp')
                        ->placeholder('Masukkan nilai perolehan aset'),

                TextInput::make('nilai_residu')
                        ->label('Nilai Residu')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(fn ($get) => $get('nilai_perolehan'))
                        ->prefix('Rp')
                        ->placeholder('Masukkan nilai residu aset')
                        ->validationMessages([
                            'max.numeric' => 'Nilai residu tidak boleh lebih besar dari nilai perolehan.',
                        ]),

                TextInput::make('masa_manfaat')
                    ->label('Masa Manfaat (dalam tahun)')
                    ->required()
                    ->numeric()
                    ->readonly(),     

                TextInput::make('metode_penyusutan')
                    ->autocapitalize('words')
                    ->label('Metode Penyusutan')
                    ->required()
                    ->readonly()
                    ->default('straight line'),
                            ]);
    }
}
