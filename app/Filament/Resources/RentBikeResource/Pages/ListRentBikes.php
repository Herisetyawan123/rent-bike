<?php

namespace App\Filament\Resources\RentBikeResource\Pages;

use App\Filament\Resources\RentBikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentBikes extends ListRecords
{
    protected static string $resource = RentBikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
