<?php

namespace App\Filament\Resources\RentBikeResource\Pages;

use App\Filament\Resources\RentBikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentBike extends EditRecord
{
    protected static string $resource = RentBikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
