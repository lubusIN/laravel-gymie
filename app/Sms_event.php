<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Sms_event extends Model
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

    protected $searchableColumns = [
        'name' => 20,
        'date' => 10,
        'message' => 5,
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
