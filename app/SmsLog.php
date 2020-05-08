<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class SmsLog extends Model
{
    use SearchableTrait;

    protected $table = 'trn_sms_log';

    protected $fillable = [
            'shoot_id',
            'number',
            'message',
            'status',
            'sender_id',
            'send_time',
    ];

    public $timestamps = false;

    protected $dates = ['send_time'];

    //Eloquence Search mapping

    protected $searchable = [
        'columns' => [
            'number' => 20,
            'message' => 10,
            'status' => 5,
        ]
    ];

    public function scopeDashboardLogs($query)
    {
        return $query->where('send_time', '<=', Carbon::now())->take(5)->orderBy('send_time', 'desc');
    }
}
