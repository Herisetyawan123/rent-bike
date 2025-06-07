<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentPaymentResource\Pages;
use App\Filament\Resources\RentPaymentResource\RelationManagers;
use App\Models\RentPayment;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentPaymentResource extends Resource
{
    protected static ?string $model = RentPayment::class;


    protected static ?string $navigationGroup = 'Transaction Management';
    protected static ?string $navigationIcon = 'heroicon-o-scale';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select::make('renter_id')
                //     ->relationship('renter.user', 'name') // Pastikan model Renter ada dan punya 'name'
                //     ->required()
                //     ->label('Renter'),
                Select::make('rent_bike_id')
                    ->relationship('rentBike', 'license_plate')
                    ->required()
                    ->label('Bike'),
                Select::make('vendor_id')
                    ->relationship('vendor', 'business_name')
                    ->required()
                    ->label('Vendor'),
                Select::make('package_id')
                    ->relationship('package', 'name')
                    ->nullable()
                    ->label('Package'),
                DatePicker::make('rent_start_date')
                    ->required()
                    ->label('Rent Start Date'),
                DatePicker::make('rent_end_date')
                    ->required()
                    ->label('Rent End Date'),
                TextInput::make('total_price')
                    ->numeric()
                    ->required()
                    ->label('Total Price'),
                Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->required()
                    ->label('Payment Status'),
                Select::make('payment_method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'cash' => 'Cash',
                        'online' => 'Online Payment',
                    ])
                    ->nullable()
                    ->label('Payment Method'),
                FileUpload::make('payment_proof')
                    ->image()
                    ->directory('payment-proofs')
                    ->label('Payment Proof')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('renter.user.name')->label('Renter'),
                TextColumn::make('rentBike.license_plate')->label('Bike'),
                TextColumn::make('vendor.business_name')->label('Vendor'),
                TextColumn::make('package.name')->label('Package')->default('-'),
                TextColumn::make('rent_start_date')->date()->label('Start Date'),
                TextColumn::make('rent_end_date')->date()->label('End Date'),
                TextColumn::make('total_price')->money('usd', true)->label('Total Price'),
                BadgeColumn::make('payment_status')
                    // ->enum([
                    //     'pending' => 'Pending',
                    //     'paid' => 'Paid',
                    //     'unpaid' => 'Unpaid',
                    //     'failed' => 'Failed',
                    // ])
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => ['unpaid', 'failed'],
                    ])
                    ->label('Payment Status'),
                TextColumn::make('payment_method')->label('Payment Method')->default('-'),
                TextColumn::make('created_at')->dateTime()->label('Created At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListRentPayments::route('/'),
            'create' => Pages\CreateRentPayment::route('/create'),
            'edit' => Pages\EditRentPayment::route('/{record}/edit'),
        ];
    }
}
