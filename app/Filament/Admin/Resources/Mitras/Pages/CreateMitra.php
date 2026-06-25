<?php

namespace App\Filament\Admin\Resources\Mitras\Pages;

use App\Filament\Admin\Resources\Mitras\MitraResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMitra extends CreateRecord
{
    protected static string $resource = MitraResource::class;
    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}

}
