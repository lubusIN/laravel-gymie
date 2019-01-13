<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    use createdByUser, updatedByUser;

    protected $table = 'trn_enquiry_followups';

    protected $fillable = [
        'enquiry_id',
        'followup_by',
        'due_date',
        'status',
        'outcome',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'due_date'];

    public function enquiry()
    {
        return $this->belongsTo('App\Enquiry', 'enquiry_id');
    }

    public function scopeReminders($query)
    {
        return $query->leftJoin('mst_enquiries', 'trn_enquiry_followups.enquiry_id', '=', 'mst_enquiries.id')->select('trn_enquiry_followups.*', 'mst_enquiries.status')->where('trn_enquiry_followups.due_date', '<=', Carbon::today())->where('trn_enquiry_followups.status', '=', \constFollowUpStatus::Pending)->where('mst_enquiries.status', '=', \constEnquiryStatus::Lead);
    }
}
