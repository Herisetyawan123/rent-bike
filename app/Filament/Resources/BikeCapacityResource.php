<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikeCapacityResource\Pages;
use App\Filament\Resources\BikeCapacityResource\RelationManagers;
use App\Models\BikeCapacity;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BikeCapacityResource extends Resource
{
    protected static ?string $model = BikeCapacity::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = 'Bike Management';
    protected static ?string $navigationLabel = 'Bike Capacity';
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('admin');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('capacity')
                    ->numeric()
                    ->label('capacity')
                    ->required(),
                TextInput::make('description')
                    ->label('description')
                    ->required(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('bikes');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('capacity')
                    ->label('Bike Capacity'),
                TextColumn::make('bikes_count')
                    ->label('Number of Bikes')
                    ->counts('bikes'),
                TextColumn::make('description')
                    ->label('description'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBikeCapacities::route('/'),
            'create' => Pages\CreateBikeCapacity::route('/create'),
            'edit' => Pages\EditBikeCapacity::route('/{record}/edit'),
        ];
    }
}
