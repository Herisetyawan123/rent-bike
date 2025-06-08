<?php

namespace App\Filament\Resources\BikeCapacityResource\Pages;

use App\Filament\Resources\BikeCapacityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBikeCapacities extends ListRecords
{
    protected static string $resource = BikeCapacityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
