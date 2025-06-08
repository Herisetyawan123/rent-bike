<?php

namespace App\Filament\Resources\BikeTypeResource\Pages;

use App\Filament\Resources\BikeTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBikeTypes extends ListRecords
{
    protected static string $resource = BikeTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
