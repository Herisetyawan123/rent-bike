<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if(isset($data['status']) && $data['status'] === 'completed') {
            $bike = \App\Models\Bike::find($data['bike_id']);
            if ($bike) {
                $bike->update(['availability_status' => 'available']);
            }
        }
        return $data;
    }
}
