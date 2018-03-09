<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role_user extends Model
{
    protected $table = 'role_user';

    public $timestamps = false;

    protected $fillable = [
            'user_id',
            'role_id',
    ];

    public function User()
    {
        return $this->belongsTo('App\User');
    }

    public function Role()
    {
        return $this->belongsTo('App\Role');
    }
}
