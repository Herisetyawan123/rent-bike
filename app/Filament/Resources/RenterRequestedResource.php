<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RenterRequestedResource\Pages;
use App\Models\Renter;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibraryPro\Forms\Components\Media as SpatieMediaLibraryImage;

class RenterRequestedResource extends Resource
{
    protected static ?string $model = Renter::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'Renter requested';
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

                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('user.name')->label('Name'),
                Tables\Columns\TextColumn::make('user.email')->label('Email'),
                Tables\Columns\TextColumn::make('user.renter.national_id')->label('NIK / Passport'),
                Tables\Columns\TextColumn::make('user.renter.driver_license_number')->label('Driver License Number'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Requested At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Approve')
                    ->action(fn(Model $record) => $record->user()->update(['is_requested' => false]))
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Action::make('Lihat')
                    ->url(fn($record) => static::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make(),
            ])->actionsPosition(ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user', function ($query) {
                $query->where('is_requested', true);
            });
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
            'index' => Pages\ListRenterRequesteds::route('/'),
            'create' => Pages\CreateRenterRequested::route('/create'),
            'view' => Pages\ViewRenterRequested::route('/{record}'),
            // 'edit' => Pages\EditRenterRequested::route('/{record}/edit'),
        ];
    }
}
