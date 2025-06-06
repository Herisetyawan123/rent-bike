<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentBikeResource\Pages;
use App\Filament\Resources\RentBikeResource\RelationManagers;
use App\Models\RentBike;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentBikeResource extends Resource
{
    protected static ?string $model = RentBike::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Bike Management';

    protected static ?string $navigationLabel = 'Bike';

    public static function form(Form $form): Form
    {
        return $form->schema([

            TextInput::make('brand')
                ->required()
                ->maxLength(255),

            TextInput::make('model')
                ->required()
                ->maxLength(255),

            TextInput::make('year')
                ->label('Year')
                ->required()
                ->numeric()
                ->maxLength(4),

            TextInput::make('license_plate')
                ->label('License Plate')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('color')
                ->maxLength(50),

            TextInput::make('rental_price_per_day')
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
                TextColumn::make('brand')->sortable()->searchable(),
                TextColumn::make('model')->sortable()->searchable(),
                TextColumn::make('year')->sortable(),
                TextColumn::make('license_plate')->sortable()->searchable(),
                TextColumn::make('color'),
                TextColumn::make('rental_price_per_day')->money('usd', true),
                BadgeColumn::make('availability_status')
                    ->colors([
                        'success' => 'available',
                        'danger' => 'rented',
                    ]),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }


    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

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
