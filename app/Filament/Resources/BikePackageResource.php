<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikePackageResource\Pages;
use App\Filament\Resources\BikePackageResource\RelationManagers;
use App\Models\BikePackage;
use App\Models\RentBike;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BikePackageResource extends Resource
{
    protected static ?string $model = BikePackage::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = 'Bike Management';

    protected static ?string $navigationLabel = 'Bike Packages';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('rent_bike_id')
                ->options(function () {
                    return \App\Models\Bike::all()->pluck('brand', 'id');
                })
                ->label('Bike')
                ->searchable()
                ->required(),

                Select::make('unit_id')
                    ->label('Unit')
                    ->options(Unit::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                TextInput::make('duration')
                    ->label('Duration')
                    ->numeric()
                    ->required()
                    ->suffix('unit'),

                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Textarea::make('description')
                    ->label('Description')
                    ->nullable()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rentBike.brand')->label('Bike')->searchable(),
                TextColumn::make('unit.name')->label('Unit'),
                TextColumn::make('duration')->label('Duration')->sortable(),
                TextColumn::make('price')->label('Price')->money('IDR'),
                TextColumn::make('description')->label('Description')->limit(30),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBikePackages::route('/'),
            'create' => Pages\CreateBikePackage::route('/create'),
            'edit' => Pages\EditBikePackage::route('/{record}/edit'),
        ];
    }
}
