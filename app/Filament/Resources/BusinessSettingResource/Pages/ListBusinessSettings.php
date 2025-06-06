<?php

namespace App\Filament\Resources\BusinessSettingResource\Pages;

use App\Filament\Resources\BusinessSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessSettings extends ListRecords
{
    protected static string $resource = BusinessSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
