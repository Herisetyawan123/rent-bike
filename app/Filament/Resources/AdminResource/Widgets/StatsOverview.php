<?php

namespace App\Filament\Resources\AdminResource\Widgets;

use App\Models\RentBike;
use Filament\Forms\Components\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Card::make('Jumlah Kendaraan', RentBike::count()),
        ];
    }
}
