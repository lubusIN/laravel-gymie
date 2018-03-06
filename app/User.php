<?php

namespace App;

use Auth;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Lubus\Constants\Status;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, HasMediaConversions
{
    use Authenticatable, CanResetPassword, EntrustUserTrait, HasMediaTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mst_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password','status','photo'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    // Media i.e. Image size conversion
    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->setManipulations(['w' => 50, 'h' => 50, 'q' => 100, 'fit' => 'crop'])
             ->performOnCollections('staff');

        $this->addMediaConversion('form')
             ->setManipulations(['w' => 70, 'h' => 70, 'q' => 100, 'fit' => 'crop'])
             ->performOnCollections('staff');
    }

    public function scopeExcludeArchive($query)
    {
        if (Auth::User()->id != 1) 
        {
            return $query->where('status','!=', \constStatus::Archive)->where('id','!=',1);
        }

        return $query->where('status','!=', \constStatus::Archive);
    }

    public function Role_user()
    {
        return $this->hasOne('App\Role_user');
    }
}
