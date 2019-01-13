<?php

namespace App;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    protected $table = 'permissions';

    protected $fillable = [
            'name',
            'display_name',
            'description',
            'group_key',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Role');
    }
}
