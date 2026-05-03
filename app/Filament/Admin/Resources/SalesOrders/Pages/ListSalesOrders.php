<?php

namespace App\Filament\Admin\Resources\SalesOrders\Pages;

use App\Filament\Admin\Resources\SalesOrders\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalesOrders extends ListRecords
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Create action dinonaktifkan karena SO dibuat otomatis oleh sistem
            // Actions\CreateAction::make(),
        ];
    }
}
