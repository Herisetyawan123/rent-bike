<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikeMerkResource\Pages;
use App\Filament\Resources\BikeMerkResource\RelationManagers;
use App\Models\BikeMerk;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BikeMerkResource extends Resource
{
    protected static ?string $model = BikeMerk::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationGroup = 'Bike Management';
    protected static ?string $navigationLabel = 'Bike Merks';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Bike Merk'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
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
            'index' => Pages\ListBikeMerks::route('/'),
            'create' => Pages\CreateBikeMerk::route('/create'),
            'edit' => Pages\EditBikeMerk::route('/{record}/edit'),
        ];
    }
}
