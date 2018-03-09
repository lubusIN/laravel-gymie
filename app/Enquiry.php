<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $table = 'mst_enquiries';

    protected $fillable = [
        'name',
        'DOB',
        'email',
        'address',
        'status',
        'gender',
        'contact',
        'pin_code',
        'occupation',
        'start_by',
        'interested_in',
        'aim',
        'source',
        'created_by',
        'updated_by',
    ];

    //Eloquence Search mapping
    use Eloquence;

    protected $searchableColumns = [
        'name' => 20,
        'email' => 20,
        'contact' => 20,
    ];

    public function Followups()
    {
        return $this->hasMany('App\Followup');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function scopeIndexQuery($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->select('id', 'name', 'contact', 'email', 'address', 'gender', 'created_at', 'status')->orderBy($sorting_field, $sorting_direction);
        }

        return $query->select('id', 'name', 'contact', 'email', 'address', 'gender', 'created_at', 'status')->whereBetween('created_at', [$drp_start, $drp_end])->orderBy($sorting_field, $sorting_direction);
    }

    public function scopeOnlyLeads($query)
    {
        return $query->where('status', '=', \constEnquiryStatus::Lead)->orderBy('created_at', 'desc')->take(10);
    }
}
