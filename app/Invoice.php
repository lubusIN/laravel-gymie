<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'trn_invoice';

    protected $fillable = [
            'total',
            'pending_amount',
            'member_id',
            'note',
            'status',
            'tax',
            'additional_fees',
            'invoice_number',
            'discount_percent',
            'discount_amount',
            'discount_note',
            'created_by',
            'updated_by',
     ];

    protected $dates = ['created_at', 'updated_at'];

    //Eloquence Search mapping
    use Eloquence;

    protected $searchableColumns = [
        'invoice_number' => 20,
        'total' => 20,
        'pending_amount' => 20,
        'Member.name' => 15,
        'Member.member_code' => 10,
    ];

    public function scopeIndexQuery($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->leftJoin('mst_members', 'trn_invoice.member_id', '=', 'mst_members.id')->select('trn_invoice.*', 'mst_members.name as member_name')->orderBy($sorting_field, $sorting_direction);
        }

        return $query->leftJoin('mst_members', 'trn_invoice.member_id', '=', 'mst_members.id')->select('trn_invoice.*', 'mst_members.name as member_name')->whereBetween('trn_invoice.created_at', [$drp_start, $drp_end])->orderBy($sorting_field, $sorting_direction);
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function Member()
    {
        return $this->belongsTo('App\Member', 'member_id');
    }

    public function PaymentDetails()
    {
        return $this->hasMany('App\PaymentDetail');
    }

    public function InvoiceDetails()
    {
        return $this->hasMany('App\InvoiceDetail');
    }

    public function Subscription()
    {
        return $this->hasOne('App\Subscription');
    }
}
