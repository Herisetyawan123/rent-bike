<?php

namespace App\Filament\Resources\BikeRequestResource\Pages;

use App\Filament\Resources\BikeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBikeRequest extends EditRecord
{
    protected static string $resource = BikeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
