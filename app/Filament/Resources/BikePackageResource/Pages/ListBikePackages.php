<?php

namespace App\Filament\Resources\BikePackageResource\Pages;

use App\Filament\Resources\BikePackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBikePackages extends ListRecords
{
    protected static string $resource = BikePackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
