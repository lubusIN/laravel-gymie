<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Member extends Model implements HasMediaConversions
{
    use HasMediaTrait , Eloquence;
    
	protected $table = 'mst_members';

	protected $fillable = [
		'member_code',
		'name',
		'DOB',
		'email',
		'address',
		'status',
		'proof_name',
		'gender',
        'contact',
        'emergency_contact',
		'health_issues',
		'pin_code',
		'occupation',
		'aim',
		'source',
		'created_by',
    	'updated_by'
	];   
    
	protected $dates  = ['created_at', 'updated_at', 'DOB'];


	protected $searchableColumns = [
	    'member_code' => 20,
        'name' => 20,
	    'email' => 20,
        'contact' => 20,
    ];

    // Media i.e. Image size conversion
    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->setManipulations(['w' => 50, 'h' => 50, 'q' => 100, 'fit' => 'crop'])
             ->performOnCollections('profile');

        $this->addMediaConversion('form')
             ->setManipulations(['w' => 70, 'h' => 70, 'q' => 100, 'fit' => 'crop'])
             ->performOnCollections('profile','proof');
    }

    //Relationships
	public function createdBy()
    {
        return $this->belongsTo('App\User','created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User','updated_by');
    }

    public function Subscriptions()
    {
    	return $this->hasMany('App\Subscription');
    }

    public function Invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    //Scope Queries
    public function scopeIndexQuery($query,$sorting_field,$sorting_direction,$drp_start,$drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) 
        {
            return $query->select('mst_members.id','mst_members.member_code','mst_members.name','mst_members.contact','mst_members.created_at','mst_members.status')->where('mst_members.status','!=', \constStatus::Archive)->orderBy($sorting_field,$sorting_direction);
        }

        return $query->select('mst_members.id','mst_members.member_code','mst_members.name','mst_members.contact','mst_members.created_at','mst_members.status')->where('mst_members.status','!=', \constStatus::Archive)->whereBetween('mst_members.created_at', [$drp_start, $drp_end])->orderBy($sorting_field,$sorting_direction);
    }


    // public function scopeReportQuery($query,$sorting_field,$sorting_direction,$drp_start,$drp_end)
    // {
    //     $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
    //     $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

    //     if ($drp_start == null or $drp_end == null) 
    //     {
    //         return $query->leftJoin('mst_plans', 'mst_members.plan_id', '=', 'mst_plans.id')->select('mst_members.*','mst_plans.plan_name')->where('mst_members.status','!=', \constStatus::Archive)->orderBy($sorting_field,$sorting_direction);
    //     }

    //     return $query->leftJoin('mst_plans', 'mst_members.plan_id', '=', 'mst_plans.id')->select('mst_members.*','mst_plans.plan_name')->where('mst_members.status','!=', \constStatus::Archive)->whereBetween('mst_members.created_at', [$drp_start, $drp_end])->orderBy($sorting_field,$sorting_direction);
    // }

    public function scopeActive($query)
    {
       return $query->where('status','=', \constStatus::Active);
    }

    public function scopeInactive($query)
    {
       return $query->where('status','=', \constStatus::InActive);
    }

    public function scopeRecent($query)
    {
       return $query->where('created_at','<=',Carbon::today())->take(10)->orderBy('created_at','desc');
    }

    public function scopeBirthday($query)
    {
    	return $query->whereMonth('DOB','=',Carbon::today()->month)->whereDay('DOB','<',Carbon::today()->addDays(7))->whereDay('DOB','>=',Carbon::today()->day)->where('status','=',\constStatus::Active);
    }

    // Laravel issue: Workaroud Needed
    public function scopeRegistrations($query,$month,$year)
    {
    	return $query->whereMonth('created_at', '=', $month)->whereYear('created_at', '=',$year)->count();
    }

}