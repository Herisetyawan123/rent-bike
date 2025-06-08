<?php

namespace App\Filament\Resources\BikeColorResource\Pages;

use App\Filament\Resources\BikeColorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBikeColor extends EditRecord
{
    protected static string $resource = BikeColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
