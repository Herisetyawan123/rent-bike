<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikeRequestResource\Pages;
use App\Filament\Resources\BikeRequestResource\RelationManagers;
use App\Models\Bike;
use App\Models\BikeRequest;
use Filament\Forms;
use Filament\Forms\Components\Select;
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
        return $form
            ->schema([
                //
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
