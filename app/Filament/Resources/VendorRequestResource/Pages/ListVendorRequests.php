<?php

namespace App\Filament\Resources\VendorRequestResource\Pages;

use App\Filament\Resources\VendorRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorRequests extends ListRecords
{
    protected static string $resource = VendorRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
