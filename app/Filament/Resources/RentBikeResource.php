<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentBikeResource\Pages;
use App\Models\Bike;
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
use Illuminate\Support\Facades\Auth;

class RentBikeResource extends Resource
{
    protected static ?string $model = Bike::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Bike Management';

    protected static ?string $navigationLabel = 'Bike';

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
                TextColumn::make('bikeMerk.name')->label('Merk'),
                TextColumn::make('bikeType.name')->label('Type'),
                TextColumn::make('bikeColor.color')->label('Color'),
                TextColumn::make('bikeCapacity.capacity')->label('Capacity'),
                TextColumn::make('year'),
                TextColumn::make('license_plate'),
                TextColumn::make('price')->money('usd', true),
                BadgeColumn::make('status')
                    ->colors([
                        'primary',
                        'success' => 'accepted',
                        'danger' => 'requested',
                    ]),
                BadgeColumn::make('availability_status')
                    ->colors([
                        'primary',
                        'success' => 'available',
                        'warning' => 'rented',
                    ]),
                TextColumn::make('addOns.name')
                    ->label('Add-ons')
                    ->wrap()
                    ->limit(50)
                    ->sortable(false),
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
                    ->color('primary'),
                Tables\Actions\Action::make('editStatusAvaibility')
                    ->label('Edit Status Avaible')
                    ->form([
                        Select::make('availability_status')
                            ->options([
                                'available' => 'Available',
                                'rented' => 'Rented',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, $data) {
                        $record->update($data);
                    })
                    ->modalHeading('Edit Status')
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', $user->id);
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
            'index' => Pages\ListRentBikes::route('/'),
            'create' => Pages\CreateRentBike::route('/create'),
            'edit' => Pages\EditRentBike::route('/{record}/edit'),
        ];
    }
}
