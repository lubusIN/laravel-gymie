<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class ChequeDetail extends Model
{
    //Eloquence Search mapping
    use Eloquence;
    use updatedByUser;

    protected $table = 'trn_cheque_details';

    protected $fillable = [
            'payment_id',
            'number',
            'date',
            'status',
            'created_by',
            'updated_by',
     ];

    protected $searchableColumns = [
        'number' => 20,
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
