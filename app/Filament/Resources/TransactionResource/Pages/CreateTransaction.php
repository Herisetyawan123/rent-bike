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
        $bike = \App\Models\RentBike::find($data['rent_bike_id']);
        $startDate = \Carbon\Carbon::parse($data['start_date']);
        $rentalDays = $data['rental_days'];
        $deliveryFee = $data['delivery_fee'] ?? 0;
        $totalTax = $data['total_tax'] ?? 0;

        $endDate = $startDate->addDays((int) $rentalDays);
        $finalTotal = ($bike->rental_price_per_day * $rentalDays) + $deliveryFee + $totalTax;

        $data['end_date'] = $endDate;
        $data['final_total'] = $finalTotal;
        $data['vendor_id'] = Auth::user()->id;

        return $data;
    }
}
