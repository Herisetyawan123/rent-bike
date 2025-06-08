<?php

namespace App\Filament\Resources\ContractLatterResource\Pages;

use App\Filament\Resources\ContractLatterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContractLatter extends EditRecord
{
    protected static string $resource = ContractLatterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
