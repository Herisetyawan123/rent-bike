<?php

namespace App\Filament\Resources\ContractLatterResource\Pages;

use App\Filament\Resources\ContractLatterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateContractLatter extends CreateRecord
{
    protected static string $resource = ContractLatterResource::class;

        protected function mutateFormDataBeforeCreate(array $data): array
    {
        $id = Auth::user()->id;
        $data['vendor_id'] = $id;

        return $data;
    }
}
