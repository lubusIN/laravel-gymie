<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = 'trn_invoice_details';

    protected $fillable = [
            'item_name',
            'plan_id',
            'item_description',
            'invoice_id',
            'item_amount',
            'created_by',
            'updated_by',
     ];

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function Invoice()
    {
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }

    public function Plan()
    {
        return $this->belongsTo('App\Plan', 'plan_id');
    }
}
