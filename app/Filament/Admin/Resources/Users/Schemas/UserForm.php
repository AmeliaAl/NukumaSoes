<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Akun')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Lengkap'),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        \Filament\Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->label('Password (Isi untuk mengubah)'),
                        \Filament\Forms\Components\Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'pemilik' => 'Pemilik',
                                'aset' => 'Admin Aset',
                                'penjualan' => 'Admin Penjualan',
                                'persediaan' => 'Admin Persediaan',
                                'produksi' => 'Admin Produksi',
                                'pembelian' => 'Admin Pembelian',
                            ])
                            ->required()
                            ->default('admin')
                            ->label('Peran Akses'),
                    ])->columns(2),
            ]);
    }
}
