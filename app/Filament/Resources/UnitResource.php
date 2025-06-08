<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
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

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Bike Management';
    protected static ?string $modelLabel = 'Unit';
    protected static ?string $pluralModelLabel = 'Units';

        public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->label('Unit Name'),

            Select::make('parent_id')
                ->label('Base Unit')
                ->relationship('parentUnit', 'name')
                ->searchable()
                ->nullable(),

            TextInput::make('multiplier')
                ->numeric()
                ->label('Multiplier')
                ->nullable()
                ->helperText('e.g. Minggu = 7 Hari, maka multiplier = 7'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Unit Name')->searchable(),
            TextColumn::make('parentUnit.name')->label('Base Unit'),
            TextColumn::make('multiplier'),
        ])
        ->filters([])
        ->defaultSort('id', 'asc');
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
