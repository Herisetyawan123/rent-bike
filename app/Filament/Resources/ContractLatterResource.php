<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractLatterResource\Pages;
use App\Filament\Resources\ContractLatterResource\RelationManagers;
use App\Models\ContractLatter;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractLatterResource extends Resource
{
    protected static ?string $model = ContractLatter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('vendor');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required(),

            Forms\Components\FileUpload::make('file_path')
                ->label('Upload Template PDF')
                ->directory('vendor-templates')
                ->preserveFilenames()
                ->acceptedFileTypes(['application/pdf'])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->url(fn (ContractLatter $record) => Storage::url($record->file_path))
                    ->openUrlInNewTab()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                self::generateContractAction(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected static function generateContractAction(): Action
    {
        return Action::make('generate_contract')
            ->label('Generate Contract PDF')
            ->action(function (ContractLatter $record) {
                $vendor = Auth::user()->vendor;

                $parser = new Parser();
                $text = $parser->parseFile(storage_path("app/public/{$record->file_path}"))->getText();

                $replaced = str_replace(
                    ['[Nama]', '[Alamat]', '[Tanggal]'],
                    ["Heri Setyawan", "Jakarta", now()->format('d-m-Y')],
                    $text
                );

                $html = "<pre style='font-family: sans-serif; white-space: pre-wrap;'>$replaced</pre>";

                $pdf = Pdf::loadHTML($html);
                $generatedPath = 'contracts/generated_' . time() . '.pdf';
                dd($generatedPath);
                Storage::put($generatedPath, $pdf->output());

                Notification::make()
                    ->success()
                    ->title('Contract Generated')
                    ->body('Kontrak berhasil digenerate. Silakan unduh file.');

                return response()->download(storage_path("app/$generatedPath"));
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
            'index' => Pages\ListContractLatters::route('/'),
            'create' => Pages\CreateContractLatter::route('/create'),
            'edit' => Pages\EditContractLatter::route('/{record}/edit'),
        ];
    }
}
