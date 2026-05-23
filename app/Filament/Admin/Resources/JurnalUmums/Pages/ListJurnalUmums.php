<?php

namespace App\Filament\Admin\Resources\JurnalUmums\Pages;

use App\Filament\Admin\Resources\JurnalUmums\JurnalUmumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJurnalUmums extends ListRecords
{
    protected static string $resource = JurnalUmumResource::class;

    public function getTitle(): string
    {
        return 'Jurnal Umum';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <div class="text-center w-full flex flex-col items-center justify-center pt-4">
                <h1 class="text-2xl font-bold text-black dark:text-white">PT XYZ</h1>
                <h2 class="text-xl font-semibold text-black dark:text-white mt-1">Jurnal Umum</h2>
                <p class="text-md text-gray-800 dark:text-gray-300 mt-1">Tanggal/Bulan/Tahun</p>
            </div>
        ');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Button "Buat" dihapus — jurnal dibuat otomatis oleh sistem
        ];
    }
}
