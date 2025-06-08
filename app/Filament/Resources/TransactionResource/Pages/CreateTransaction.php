<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $bike = \App\Models\Bike::find($data['bike_id']);
        $bike->availability_status = 'rented';
        $bike->save();
        $startDate = \Carbon\Carbon::parse($data['start_date']);
        $rentalDays = $data['rental_days'];
        $deliveryFee = $data['delivery_fee'] ?? 0;
        // $totalTax = $data['total_tax'] ?? 0
        
        $totalTax = getSetting('app_tax');

        $endDate = $startDate->addDays((int) $rentalDays);
        $finalTotal = (($bike->price * $rentalDays) + $deliveryFee) * ((100 - floatval($totalTax)) / 100);

        $data['end_date'] = $endDate;
        $data['final_total'] = $finalTotal;
        $data['vendor_id'] = Auth::user()->id;

        return $data;
    }
}
