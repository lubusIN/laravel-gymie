<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class SmsTrigger extends Model
{
    //Eloquence Search
    use createdByUser, updatedByUser, SearchableTrait;

    const CREATED_AT = null;

    protected $table = 'mst_sms_triggers';

    protected $fillable = [
        'name',
        'alias',
        'message',
        'status',
        'updated_by',
    ];

    protected $searchable = [
        'columns' => [
            'name' => 20,
            'message' => 10
        ]
    ];
}
