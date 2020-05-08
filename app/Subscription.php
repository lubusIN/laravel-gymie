<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Subscription extends Model
{
    //Eloquence Search mapping
    use createdByUser, updatedByUser, SearchableTrait;

    protected $table = 'trn_subscriptions';

    protected $fillable = [
        'member_id',
        'invoice_id',
        'plan_id',
        'status',
        'is_renewal',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['created_at', 'updated_at', 'start_date', 'end_date'];

    protected $searchable = [
        'columns' => [
            'members.member_code' => 20,
            'members.name' => 20,
            'start_date' => 20,
            'end_date' => 20,
            'plans.plan_name' => 20,
            'invoices.invoice_number' => 20,
        ],
        'joins' => [
            'mst_members as members'  => [ 'trn_subscriptions.member_id','members.id' ],
            'mst_plans as plans'      => [ 'trn_subscriptions.plan_id','plans.id' ],
            'trn_invoice as invoices' => [ 'trn_subscriptions.invoice_id','invoices.id' ]
        ]
    ];

    public function scopeDashboardExpiring($query)
    {
        return $query
            ->with(['member' => function ($query) {
                $query->where('status', '=', \constStatus::Active);
            }])
            ->where('end_date', '<', Carbon::today()->addDays(7))
            ->where('status', '=', \constSubscription::onGoing);
    }

    public function scopeDashboardExpired($query)
    {
        return $query
            ->with(['member' => function ($query) {
                $query->where('status', '=', \constStatus::Active);
            }])
            ->where('status', '=', \constSubscription::Expired);
    }

    public function scopeIndexQuery($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->orderBy($sorting_field, $sorting_direction);
        }

        return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->whereBetween('trn_subscriptions.created_at', [$drp_start, $drp_end])->orderBy($sorting_field, $sorting_direction);
    }

    public function scopeExpiring($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->where('trn_subscriptions.end_date', '<', Carbon::today()->addDays(7))->where('trn_subscriptions.status', '=', \constSubscription::onGoing)->orderBy($sorting_field, $sorting_direction);
        }

        return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->where('trn_subscriptions.end_date', '<', Carbon::today()->addDays(7))->where('trn_subscriptions.status', '=', \constSubscription::onGoing)->whereBetween('trn_subscriptions.created_at', [$drp_start, $drp_end])->orderBy($sorting_field, $sorting_direction);
    }

    public function scopeExpired($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->where('trn_subscriptions.status', '=', \constSubscription::Expired)->where('trn_subscriptions.status', '!=', \constSubscription::renewed)->orderBy($sorting_field, $sorting_direction);
        }

        return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->where('trn_subscriptions.status', '=', \constSubscription::Expired)->where('trn_subscriptions.status', '!=', \constSubscription::renewed)->whereBetween('trn_subscriptions.created_at', [$drp_start, $drp_end])->orderBy($sorting_field, $sorting_direction);
    }

    public function member()
    {
        return $this->belongsTo('App\Member', 'member_id');
    }

    public function plan()
    {
        return $this->belongsTo('App\Plan', 'plan_id');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }
}
