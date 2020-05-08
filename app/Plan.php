<?php

namespace App;

use Lubus\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Plan extends Model
{
    protected $table = 'mst_plans';

    protected $fillable = [
        'plan_code',
        'plan_name',
        'service_id',
        'plan_details',
        'days',
        'amount',
        'status',
        'created_by',
        'updated_by',
    ];

    // Search
    use SearchableTrait;
    use createdByUser, updatedByUser;

    protected $searchable = [
        'columns' => [
            'plan_code' => 10,
            'plan_name' => 10,
            'plan_details' => 5
        ]
    ];

    public function getPlanDisplayAttribute()
    {
        return $this->plan_code.' @ '.$this->amount.' For '.$this->days.' Days';
    }

    public function scopeExcludeArchive($query)
    {
        return $query->where('status', '!=', \constStatus::Archive);
    }

    public function scopeOnlyActive($query)
    {
        return $query->where('status', '=', \constStatus::Active);
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Subscription', 'plan_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Service', 'service_id');
    }
}
