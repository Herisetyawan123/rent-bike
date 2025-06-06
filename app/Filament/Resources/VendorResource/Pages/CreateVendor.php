<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    public static function afterSave(Model $record): void
    {
        $user = $record->user;
        if ($user && !$user->password) {
            $user->password = Hash::make('defaultpassword123'); // default password
            $user->assignRole('vendor');
            $user->save();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['user'])) {
            $userData = $data['user'];
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('defaultpassword'),
            ]);

            $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
            $user->assignRole($vendorRole);

            $data['user_id'] = $user->id;
            unset($data['user']);
        }
        return $data;
    }

}
