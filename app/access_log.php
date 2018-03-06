<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class access_log extends Model
{
    protected $table = 'trn_access_log';

    protected $fillable = [
    		'user_id',
    		'action',
    		'module',
    		'record',
    		'created_at'
    ];

    public $timestamps = false;
}
