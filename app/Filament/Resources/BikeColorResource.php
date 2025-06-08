<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikeColorResource\Pages;
use App\Filament\Resources\BikeColorResource\RelationManagers;
use App\Models\BikeColor;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BikeColorResource extends Resource
{
    protected static ?string $model = BikeColor::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Bike Management';
    protected static ?string $navigationLabel = 'Bike Colors';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('admin');
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('color')
                ->label('Color')
                ->required(),
                ColorPicker::make('color_code')
                ->label('Color Code')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('color')
                    ->label('Color'),
                TextColumn::make('color_code')
                    ->label('Color Code'),
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
            'index' => Pages\ListBikeColors::route('/'),
            'create' => Pages\CreateBikeColor::route('/create'),
            'edit' => Pages\EditBikeColor::route('/{record}/edit'),
        ];
    }
}
