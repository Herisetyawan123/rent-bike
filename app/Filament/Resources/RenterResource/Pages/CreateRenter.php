<?php

namespace App\Filament\Resources\RenterResource\Pages;

use App\Filament\Resources\RenterResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateRenter extends CreateRecord
{
    protected static string $resource = RenterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
            ]);

            $data['user_id'] = $user->id;

            $user->assignRole('renter');
            unset($data['email']);
            unset($data['password']);
            unset($data['name']);
            unset($data['phone']);
        } else {
            unset($data['password']);
        }

        logger()->info('Data to create renter:', $data);
        return $data;
    }

    public function afterSave(): void
    {
        $this->record->renter()->updateOrCreate([], $this->form->getState()['renter'] ?? []);
    }

    protected function afterCreate(): void
    {
        // Redirect ke index daripada ke edit
        $this->redirect(static::getResource()::getUrl('index'));
    }
}
