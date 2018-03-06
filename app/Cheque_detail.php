<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;

class Cheque_detail extends Model
{
	 protected $table = 'trn_cheque_details';

	 protected $fillable = [
	 		'payment_id',
	 		'number',
	 		'date',
	 		'status',
	 		'created_by',
    		'updated_by',
	 ];

     //Eloquence Search mapping
    use Eloquence;

    protected $searchableColumns = [
        'number' => 20,
    ];  

	public function createdBy()
    {
        return $this->belongsTo('App\User','created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User','updated_by');
    }  

    public function Payment()
    {
    	return $this->belongsTo('App\Payment_detail','payment_id');
    }    
}