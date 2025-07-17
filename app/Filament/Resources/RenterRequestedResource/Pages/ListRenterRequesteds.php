<?php

namespace App\Filament\Resources\RenterRequestedResource\Pages;

use App\Filament\Resources\RenterRequestedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRenterRequesteds extends ListRecords
{
    protected static string $resource = RenterRequestedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
