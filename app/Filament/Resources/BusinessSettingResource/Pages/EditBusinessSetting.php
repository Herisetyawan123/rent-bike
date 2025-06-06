<?php

namespace App\Filament\Resources\BusinessSettingResource\Pages;

use App\Filament\Resources\BusinessSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessSetting extends EditRecord
{
    protected static string $resource = BusinessSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
