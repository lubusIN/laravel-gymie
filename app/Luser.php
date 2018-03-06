<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class luser extends Model
{
    protected $table = 'mst_staff';

    protected $fillable = [
    		'username',
    		'password',
    		'name',
    		'created_by',
    		'updated_by'
    ];
}
