<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class SmsEvent extends Model
{
    use SearchableTrait;

    protected $table = 'mst_sms_events';

    protected $fillable = [
            'name',
            'date',
            'message',
            'description',
            'status',
            'send_to',
            'created_by',
            'updated_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'date'];

    //Eloquence Search mapping
    use createdByUser, updatedByUser;

    protected $searchable = [
        'columns' => [
            'name' => 20,
            'date' => 10,
            'message' => 5
        ]
    ];
}
