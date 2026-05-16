<?php

namespace App\Filament\Admin\Resources\PembayaranAsets\Pages;

use App\Filament\Admin\Resources\PembayaranAsets\PembayaranAsetResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\PembayaranAsetService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePembayaranAset extends CreateRecord
{
    protected static string $resource = PembayaranAsetResource::class;
    protected static ?string $title = 'Tambah Transaksi Pembayaran';

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

   protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $pembayaran = static::getModel()::create($data);

            PembayaranAsetService::buatJurnal($pembayaran);

            return $pembayaran;
        });
    }

}
