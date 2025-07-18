<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\ContractLatter;
use App\Models\Transaction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\Action;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaction Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('bike_id'),
                // Select::make('bike_id')
                //     ->label('Bike')
                //     ->options(function () {
                //         return \App\Models\Bike::with('bikeMerk', 'bikeType')
                //             ->where('user_id', Auth::id())
                //             ->where('status', 'accepted')
                //             ->where('availability_status', 'available')
                //             ->get()
                //             ->mapWithKeys(function ($bike) {
                //                 return [$bike->id => $bike->bikeMerk->name . ' - ' . $bike->bikeType->name. ' '. $bike->bikeCapacity->capacity . 'cc - ' . $bike->bikeColor->color];
                //             })->toArray();
                //     })
                //     ->required(),

                Select::make('customer_id')
                    ->label('Customer')
                    ->options(function () {
                        return \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'renter'))
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),

                DateTimePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),

                // TextInput::make('rental_days')
                //     ->label('Jumlah Hari Sewa')
                //     ->numeric()
                //     ->minValue(1)
                //     ->required(),

                DateTimePicker::make('end_date')
                    ->label('End Date')
                    ->disabled(), // kita hitung otomatis di backend

                // TextInput::make('total_tax')
                //     ->label('Total Tax')
                //     ->numeric()
                //     ->default(0),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'payment_pending' => 'Payment Pending',
                        'paid' => 'Paid',
                        'awaiting_pickup' => 'Awaiting Pickup',
                        'being_delivered' => 'Being Delivered',
                        'in_use' => 'In Use',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ])
                    ->default('payment_pending')
                    ->required(),

                Select::make('pickup_type')
                    ->label('Pickup Type')
                    ->options([
                        'pickup_self' => 'Pickup Self',
                        'delivery' => 'Delivery',
                    ])
                    ->default('pickup_self')
                    ->required()
                    ->reactive(),

                Textarea::make('delivery_address')
                    ->label('Delivery Address')
                    ->visible(fn (callable $get) => $get('pickup_type') === 'delivery')
                    ->nullable(),

                TextInput::make('delivery_fee')
                    ->label('Delivery Fee')
                    ->numeric()
                    ->default(0)
                    ->visible(fn (callable $get) => $get('pickup_type') === 'delivery'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bike.brand')
                    ->label('Bike')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('pickup_type')
                    ->label('Pickup Type')
                    ->formatStateUsing(fn (string $state) => $state === 'pickup_self' ? 'Pickup' : 'Delivery'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        'payment_pending' => 'warning',
                        default => 'gray',
                    })
                    ->label('Status'),

                TextColumn::make('final_total')
                    ->money('IDR') // Ubah 'IDR' ke 'USD', 'SGD', dll sesuai kebutuhan
                    ->label('Total'),
            ])
            ->actions([
                self::generateContractAction(),
            ]);
    }

    protected static function generateContractAction(): Action
    {
        return Action::make('generate_contract')
            ->label('Generate Contract PDF')
            ->action(function (Transaction $record) {
                $vendor = Auth::user();
                $template = ContractLatter::where('vendor_id', $vendor->id)->first();

                $parser = new Parser();
                $text = $parser->parseFile(storage_path("app/public/{$template->file_path}"))->getText();


                $start = Carbon::parse($record->start_date);
                $end = Carbon::parse($record->end_date);

                $jumlahHari = $start->diffInDays($end);

                $replaced = str_replace(
                    ['[Nama]', '[Alamat]','[NamaVendor]', '[AlamatVendor]', '[Tanggal]', '[Biaya]', '[JenisSepeda]', '[Durasi]'],
                    [$record->customer->name, $record->customer->renter->address,$record->vendor->name, $record->vendor->vendor->business_address, now()->format('d-m-Y'), $record->final_total, $record->bike->bikeMerk->name, $jumlahHari],
                    $text
                );

                $html = "<pre style='font-family: sans-serif; white-space: pre-wrap;'>$replaced</pre>";

                $pdf = Pdf::loadHTML($html);
                $generatedPath = 'contracts/generated_' . time() . '.pdf';

                // Simpan ke public disk
                Storage::disk('public')->put($generatedPath, $pdf->output());

                Notification::make()
                    ->success()
                    ->title('Contract Generated')
                    ->body('Kontrak berhasil digenerate. Silakan unduh file.');

                return response()->download(storage_path("app/public/$generatedPath"));
            });

    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('vendor_id', $user->id);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
