<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\hasMany;
use App\Enums\Status;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar, FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasApiTokens, SoftDeletes, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'photo',
        'name',
        'email',
        'status',
        'password',
        'contact',
        'dob',
        'gender',
        'address',
        'country',
        'city',
        'state',
        'pincode'

    ];

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
            'dob' => 'date',
            'status' => Status::class
        ];
    }

    protected $dates = ['deleted_at'];

    /**
     * Get the followUps for the user.
     *
     * @return hasMany
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get the enquiries for the user.
     *
     * @return hasMany
     */
    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    /**
     * Get the URL for the user's Filament avatar.
     *
     * @return string|null The URL of the user's avatar or null if not set.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->photo ? asset($this->photo) : null;
    }

    /**
     * Determine if the user can access the Filament panel.
     *
     * @param Panel $panel The Filament panel instance.
     * @return bool True if the user can access the panel, false otherwise.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
