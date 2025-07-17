<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RenterRequestedResource\Pages;
use App\Filament\Resources\RenterRequestedResource\RelationManagers;
use App\Models\Renter;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Action::make('confirm')
                    ->label('Confirm')
                    ->button()
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->modalHeading('Confirm Renter')
                    ->modalSubmitActionLabel('Approve')
                    ->modalCancelActionLabel('Close')
                    ->form(function (Model $record) {
                        $user = $record->user;

                        return [
                            \Filament\Forms\Components\ViewField::make('info')
                                ->view('filament.renter-confirmation')
                                ->viewData([
                                    'renter' => $record,
                                    'user' => $user,
                                    'documents' => [
                                        'national_id_front' => $user->getFirstMediaUrl('national_id_front'),
                                        'national_id_back' => $user->getFirstMediaUrl('national_id_back'),
                                        'driving_license_front' => $user->getFirstMediaUrl('driving_license_front'),
                                        'driving_license_back' => $user->getFirstMediaUrl('driving_license_back'),
                                        'selfie_with_id' => $user->getFirstMediaUrl('selfie_with_id'),
                                    ]
                                ])
                                ->columnSpanFull(),
                        ];
                    })
                    ->action(function (Model $record) {
                        $record->user->update(['is_requested' => false]); // or mark as approved, etc.
                    }),
                Tables\Actions\EditAction::make(),
            ])
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
            // 'edit' => Pages\EditRenterRequested::route('/{record}/edit'),
        ];
    }
}
