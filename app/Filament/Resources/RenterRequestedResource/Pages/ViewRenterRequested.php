<?php

namespace App\Filament\Resources\RenterRequestedResource\Pages;

use App\Filament\Resources\RenterRequestedResource;
use App\Models\Renter;
use Filament\Forms;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use Spatie\MediaLibraryPro\Forms\Components\Media as SpatieMediaLibraryImage;

class ViewRenterRequested extends ViewRecord
{
    protected static string $resource = RenterRequestedResource::class;


    protected static string $view = 'filament.resources.renter-requesteds.view';

    public function mount($record): void
    {
        $this->record = Renter::with('user')->findOrFail($record);
    }


}
