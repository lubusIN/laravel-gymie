<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_user';

    public $timestamps = false;

    protected $fillable = [
            'user_id',
            'role_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function role()
    {
        return $this->belongsTo('App\Role');
    }
}
