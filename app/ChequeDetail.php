<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class ChequeDetail extends Model
{
    //Eloquence Search
    use updatedByUser, SearchableTrait;

    protected $table = 'trn_cheque_details';

    protected $fillable = [
            'payment_id',
            'number',
            'date',
            'status',
            'created_by',
            'updated_by',
     ];

    protected $searchable = [
        'columns' => [
            'number' => 20
        ]
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function payment()
    {
        return $this->belongsTo('App\PaymentDetail', 'payment_id');
    }
}
