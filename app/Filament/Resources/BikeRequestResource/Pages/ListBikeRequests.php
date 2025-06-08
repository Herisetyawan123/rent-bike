<?php

namespace App\Filament\Resources\BikeRequestResource\Pages;

use App\Filament\Resources\BikeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBikeRequests extends ListRecords
{
    protected static string $resource = BikeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
