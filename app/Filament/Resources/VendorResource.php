<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\RelationManagers;
use App\Models\User;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'User Management';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                    Section::make('User Information')
                        ->schema([
                            TextInput::make('user.name')
                                ->required()
                                ->label('Contact Person Name')   
                                ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                    $component->state($record?->user?->name);
                                }),
                            TextInput::make('user.email')
                                ->email()
                                ->required()
                                ->unique(
                                    table: 'users',
                                    column: 'email',
                                    ignorable: fn ($record) => $record?->user
                                )
                                ->label('Email Address') ->afterStateHydrated(function (TextInput $component, $state, $record) {
                                    $component->state($record?->user?->email);
                                }),
                            // Password will be set to default after save
                        ]),
                    Section::make('Vendor Details')
                        ->schema([
                            TextInput::make('business_name')->required(),
                            TextInput::make('tax_id')->nullable()->label('Tax ID (NPWP)'),
                            TextInput::make('business_address')->required(),
                            TextInput::make('contact_person_name')->required(),
                            TextInput::make('latitude')->numeric()->nullable(),
                            TextInput::make('longitude')->numeric()->nullable(),
                            TextInput::make('phone')->required(),
                            FileUpload::make('photo_attachment') ->directory('vendor-photos')->image()->nullable()->label('Photo Attachment'),
                            TextInput::make('national_id')->required()->label('National ID (NIK)'),
                            TextInput::make('legal_documents')->nullable()->label('Legal Documents'),
                        ]),
                ]);
    }

    // public static function mutateFormDataBeforeCreate(array $data): array
    // {
    //     if (isset($data['user'])) {
    //         $userData = $data['user'];
    //         $user = User::create([
    //             'name' => $userData['name'],
    //             'email' => $userData['email'],
    //             'password' => Hash::make('defaultpassword'),
    //         ]);

    //         $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
    //         $user->assignRole($vendorRole);

    //         $data['user_id'] = $user->id;
    //         unset($data['user']);
    //     }
    //     return $data;
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Name')->sortable()->searchable(),
                TextColumn::make('business_name')->sortable()->searchable(),
                TextColumn::make('phone')->label('Phone Number'),
                ImageColumn::make('photo_attachment')->label('Photo'),
                TextColumn::make('tax_id')->label('Tax ID (NPWP)'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
