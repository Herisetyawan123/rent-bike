<?php

namespace App\Filament\Resources\RenterResource\Pages;

use App\Filament\Resources\RenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRenter extends EditRecord
{
    protected static string $resource = RenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
