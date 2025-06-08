<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RenterResource\Pages;
use App\Filament\Resources\RenterResource\RelationManagers;
use App\Models\Renter;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RenterResource extends Resource
{
    protected static ?string $model = Renter::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Renter';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Account Information')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('email')    ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->required(fn ($record) => ! $record)
                            ->label('Password'),
                    ]),


                Section::make('Renter Details')
                    ->schema([
                        TextInput::make('national_id')->label('NIK / Passport'),
                        TextInput::make('driver_license_number')->label('Driver License Number'),
                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ]),
                        TextInput::make('ethnicity')->label('Ethnicity'),
                        TextInput::make('nationality')->label('Nationality'),
                        DatePicker::make('birth_date')->label('Birth Date'),
                        Textarea::make('address')->label('Address'),
                        Textarea::make('current_address')->label('Current Address'),
                        Select::make('marital_status')
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                                'divorced' => 'Divorced',
                                'widowed' => 'Widowed',
                            ]),
                        TextInput::make('phone')->label('Phone'),
                    ]),
            ]);
    }



    public static function table(Table $table): Table
    {
    return $table
        ->columns([
            TextColumn::make('user.email')->label('Email'),
            TextColumn::make('nik_passport')->label('NIK / Passport'),
            TextColumn::make('driver_license_number')->label('Driver License'),
            TextColumn::make('gender')->label('Gender'),
            TextColumn::make('ethnicity')->label('Ethnicity'),
            TextColumn::make('nationality')->label('Nationality'),
            TextColumn::make('birth_date')->date()->label('Birth Date'),
            TextColumn::make('address')->label('Address'),
            TextColumn::make('current_address')->label('Current Address'),
            TextColumn::make('marital_status')->label('Marital Status'),
            TextColumn::make('phone')->label('Phone'),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created At'),
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
            'index' => Pages\ListRenters::route('/'),
            'create' => Pages\CreateRenter::route('/create'),
            'edit' => Pages\EditRenter::route('/{record}/edit'),
        ];
    }
}
