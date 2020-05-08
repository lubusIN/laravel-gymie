<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Invoice extends Model
{
    use createdByUser, updatedByUser, SearchableTrait;

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

    //Eloquence Search

    protected $searchable = [
        'columns' => [
            'invoice_number'          => 10,
            'total'                   => 10,
            'pending_amount'          => 10,
            'members.name'        => 10,
            'members.member_code' => 10
        ],
        'joins' => [
            'mst_members as members' => [ 'trn_invoice.member_id','members.id' ]
        ]
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

    public function member()
    {
        return $this->belongsTo('App\Member', 'member_id');
    }

    public function paymentDetails()
    {
        return $this->hasMany('App\PaymentDetail');
    }

    public function invoiceDetails()
    {
        return $this->hasMany('App\InvoiceDetail');
    }

    public function subscription()
    {
        return $this->hasOne('App\Subscription');
    }
}
