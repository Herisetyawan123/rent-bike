<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessSettingResource\Pages;
use App\Filament\Resources\BusinessSettingResource\RelationManagers;
use App\Models\BusinessSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusinessSettingResource extends Resource
{
    protected static ?string $model = BusinessSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Business Settings';
    protected static ?string $pluralModelLabel = 'Business Settings';

    protected static ?string $navigationGroup = 'Business Settings Management';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('setting_key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit') // agar tidak bisa diganti saat edit
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('setting_value')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Isi sesuai kebutuhan, misalnya: angka margin, alamat, email, dll.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('setting_key')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('setting_value')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y H:i')->label('Last Update'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBusinessSettings::route('/'),
            'create' => Pages\CreateBusinessSetting::route('/create'),
            'edit' => Pages\EditBusinessSetting::route('/{record}/edit'),
        ];
    }
}
