<?php

namespace App\Filament\Resources\BikeMerkResource\Pages;

use App\Filament\Resources\BikeMerkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBikeMerk extends EditRecord
{
    protected static string $resource = BikeMerkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
