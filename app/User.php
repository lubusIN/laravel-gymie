<?php

namespace App;

use Auth;
use Lubus\Constants\Status;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasMedia
{
    use Authenticatable, 
        CanResetPassword, 
        HasRoleAndPermission,
        HasMediaTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $appends = [
        'photoProfile'
    ];

    public function getPhotoProfileAttribute()
    {
        $images = $this->getMedia('staff');
        if($images->isEmpty()) return url('assets/img/web/profile-default.png');
        return $images->last()->getFullUrl();
    }

    public function scopeExcludeArchive($query)
    {
        if (Auth::User()->id != 1) {
            return $query->where('status', '!=', \constStatus::Archive)->where('id', '!=', 1);
        }

        return $query->where('status', '!=', \constStatus::Archive);
    }

}
