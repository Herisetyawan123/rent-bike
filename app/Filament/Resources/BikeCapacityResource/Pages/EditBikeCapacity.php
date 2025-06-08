<?php

namespace App\Filament\Resources\BikeCapacityResource\Pages;

use App\Filament\Resources\BikeCapacityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBikeCapacity extends EditRecord
{
    protected static string $resource = BikeCapacityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
