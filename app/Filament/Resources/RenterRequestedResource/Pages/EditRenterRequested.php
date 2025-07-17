<?php

namespace App\Filament\Resources\RenterRequestedResource\Pages;

use App\Filament\Resources\RenterRequestedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRenterRequested extends EditRecord
{
    protected static string $resource = RenterRequestedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
