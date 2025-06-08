<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikeRequestResource\Pages;
use App\Filament\Resources\BikeRequestResource\RelationManagers;
use App\Models\Bike;
use App\Models\BikeRequest;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BikeRequestResource extends Resource
{
    protected static ?string $model = Bike::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Bike Management';

    protected static ?string $navigationLabel = 'Bike Requested';
    public static function form(Form $form): Form
    {
        return $form->schema([

            // Merk
            Select::make('bike_merk_id')
                ->label('Bike Merk')
                ->relationship('bikeMerk', 'name')
                ->required(),

            // Type
            Select::make('bike_type_id')
                ->label('Bike Type')
                ->relationship('bikeType', 'name')
                ->required(),

            // Color
            Select::make('bike_color_id')
                ->label('Bike Color')
                ->relationship('bikeColor', 'color')
                ->required(),

            // Capacity
            Select::make('bike_capacity_id')
                ->label('Bike Capacity')
                ->relationship('bikeCapacity', 'capacity')
                ->required(),

            TextInput::make('year')
                ->label('Year')
                ->required()
                ->numeric()
                ->maxLength(4),

            TextInput::make('license_plate')
                ->label('License Plate')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('price')
                ->label('Rental Price Per Day')
                ->required()
                ->numeric(),

            Select::make('availability_status')
                ->label('Availability Status')
                ->options([
                    'available' => 'Available',
                    'rented' => 'Rented',
                ])
                ->required(),



            CheckboxList::make('addOns')
                ->label('Add-ons')
                ->relationship('addOns', 'name')
                ->columns(2),

            Textarea::make('description')
                    ->label('Description'),

            FileUpload::make('photo')
                ->image()
                ->directory('rent-bike-photos')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->size(50),
                TextColumn::make('bikeType.name')->label('Bike Type')->sortable()->searchable(),
                TextColumn::make('bikeMerk.name')->label('Bike Merk')->sortable()->searchable(),
                TextColumn::make('status')->label('Status')->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'primary',
                        'success' => 'accepted',
                        'danger' => 'requested',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('editStatus')
                    ->label('Edit Status')
                    ->form([
                        Select::make('status')
                            ->options([
                                'requested' => 'Requested',
                                'accepted' => 'Accepted',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, $data) {
                        $record->update($data);
                    })
                    ->modalHeading('Edit Status')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->visible(function () {
                        return Auth::user()->hasRole('admin');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }


    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return parent::getEloquentQuery()->where('status', 'requested');
        }

        return parent::getEloquentQuery()->where('user_id', $user->id)->where('status', 'requested');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBikeRequests::route('/'),
            'create' => Pages\CreateBikeRequest::route('/create'),
            'edit' => Pages\EditBikeRequest::route('/{record}/edit'),
        ];
    }
}
