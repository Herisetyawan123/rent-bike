<?php

namespace App\Filament\Resources\VendorRequestResource\Pages;

use App\Filament\Resources\VendorRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorRequest extends EditRecord
{
    protected static string $resource = VendorRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
