<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikeTypeResource\Pages;
use App\Filament\Resources\BikeTypeResource\RelationManagers;
use App\Models\BikeType;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BikeTypeResource extends Resource
{
    protected static ?string $model = BikeType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Bike Management';
    protected static ?string $navigationLabel = 'Bike Types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('bike_merk_id')
                    ->label('Merk')
                    ->relationship('merk', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('Type')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Bike Merk Type'),
                TextColumn::make('merk.name')
                    ->label('Bike Merk'),
                TextColumn::make('bikes_count')
                    ->label('Number of Bikes')
                    ->counts('bikes'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('bikes');
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
            'index' => Pages\ListBikeTypes::route('/'),
            'create' => Pages\CreateBikeType::route('/create'),
            'edit' => Pages\EditBikeType::route('/{record}/edit'),
        ];
    }
}
