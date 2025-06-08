<?php

namespace App\Filament\Resources\ContractLatterResource\Pages;

use App\Filament\Resources\ContractLatterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContractLatters extends ListRecords
{
    protected static string $resource = ContractLatterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
