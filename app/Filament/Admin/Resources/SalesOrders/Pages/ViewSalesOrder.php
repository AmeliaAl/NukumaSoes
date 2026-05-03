<?php

namespace App\Filament\Admin\Resources\SalesOrders\Pages;

use App\Filament\Admin\Resources\SalesOrders\SalesOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesOrder extends ViewRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Edit action dinonaktifkan karena SO tidak boleh diedit manual
            // Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
