<?php

namespace App\Filament\Resources\BikeColorResource\Pages;

use App\Filament\Resources\BikeColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBikeColors extends ListRecords
{
    protected static string $resource = BikeColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
