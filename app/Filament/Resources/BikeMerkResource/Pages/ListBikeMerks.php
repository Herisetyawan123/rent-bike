<?php

namespace App\Filament\Resources\BikeMerkResource\Pages;

use App\Filament\Resources\BikeMerkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBikeMerks extends ListRecords
{
    protected static string $resource = BikeMerkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
