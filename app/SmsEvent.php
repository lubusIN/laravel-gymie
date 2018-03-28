<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class SmsEvent extends Model
{
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
    use Eloquence;
    use createdByUser, updatedByUser;

    protected $searchableColumns = [
        'name' => 20,
        'date' => 10,
        'message' => 5,
    ];
}
