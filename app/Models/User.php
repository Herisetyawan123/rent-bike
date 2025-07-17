<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\FilamentUser;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements FilamentUser, HasMedia
{
    use InteractsWithMedia;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'otp',
        'otp_expires_at',
        'photo',
        'is_requested',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('national_id_front')->singleFile();
        $this->addMediaCollection('national_id_back')->singleFile();

        $this->addMediaCollection('driver_license_front')->singleFile();
        $this->addMediaCollection('driver_license_back')->singleFile();

        $this->addMediaCollection('selfie_with_id')->singleFile();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_requested' => 'boolean',
        ];
    }

    public function canAccessFilament(): bool
    {
        return $this->hasRole("admin");
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(["vendor", 'employee', 'admin']);
    }
    

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function renter()
    {
        return $this->hasOne(Renter::class);
    }

    public function checkEligibility(): array
    {
        $missing = [];

        if (empty($this->name)) $missing[] = 'name';
        if (empty($this->email)) $missing[] = 'email';
        if (empty($this->phone)) $missing[] = 'phone';

        if (!$this->renter) {
            $missing = array_merge($missing, [
                'renter.national_id',
                'renter.driver_license_number',
                'renter.gender',
                'renter.ethnicity',
                'renter.nationality',
                'renter.birth_date',
                'renter.address',
                'renter.current_address',
                'renter.marital_status',
            ]);
        } else {
            if (empty($this->renter->national_id)) $missing[] = 'renter.national_id';
            if (empty($this->renter->driver_license_number)) $missing[] = 'renter.driver_license_number';
            if (empty($this->renter->gender)) $missing[] = 'renter.gender';
            if (empty($this->renter->ethnicity)) $missing[] = 'renter.ethnicity';
            if (empty($this->renter->nationality)) $missing[] = 'renter.nationality';
            if (empty($this->renter->birth_date)) $missing[] = 'renter.birth_date';
            if (empty($this->renter->address)) $missing[] = 'renter.address';
            if (empty($this->renter->current_address)) $missing[] = 'renter.current_address';
            if (empty($this->renter->marital_status)) $missing[] = 'renter.marital_status';
        }

        return [
            'is_eligible' => $this->is_requested,
            'missing_fields' => $this->is_requested ? ["Belum disetujui"] : $missing,
        ];
    }

}
