<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Carbon\Carbon;

class Expense extends Model
{
    protected $table = 'trn_expenses';

    protected $fillable = [
    	  'name',
    	  'category_id',
          'amount',
          'due_date',
          'repeat',
          'note',
    	  'paid',
    	  'created_by',
    	  'updated_by'
    ];

    //Eloquence Search mapping
    use Eloquence;

    protected $searchableColumns = [
        'name' => 20,
        'amount' => 10,
    ];
    
    protected $dates  = ['created_at', 'updated_at', 'due_date'];

    public function createdBy()
    {
        return $this->belongsTo('App\User','created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User','updated_by');
    }

    public function Category()
    {
        return $this->belongsTo('App\ExpenseCategory','category_id');
    }

    public function scopeDueAlerts($query)
    {
        return $query->where('paid','!=',\constPaymentStatus::Paid)->where('due_date','>=',Carbon::today());
    }

    public function scopeOutstandingAlerts($query)
    {
        return $query->where('paid','!=',\constPaymentStatus::Paid)->where('due_date','<',Carbon::today());
    }

    public function scopeIndexQuery($query,$category,$sorting_field,$sorting_direction,$drp_start,$drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) 
        {
            if ($category == 0) 
            {
                return $query->leftJoin('mst_expenses_categories', 'trn_expenses.category_id', '=', 'mst_expenses_categories.id')->select('trn_expenses.*','mst_expenses_categories.name as category_name')->orderBy($sorting_field,$sorting_direction);
            } 
            else 
            {
                return $query->leftJoin('mst_expenses_categories', 'trn_expenses.category_id', '=', 'mst_expenses_categories.id')->select('trn_expenses.*','mst_expenses_categories.name as category_name')->where('category_id',$category)->orderBy($sorting_field,$sorting_direction);
            }

        }

        if ($category == 0) 
        {
            return $query->leftJoin('mst_expenses_categories', 'trn_expenses.category_id', '=', 'mst_expenses_categories.id')->select('trn_expenses.*','mst_expenses_categories.name as category_name')->whereBetween('trn_expenses.created_at', [$drp_start, $drp_end])->orderBy($sorting_field,$sorting_direction);
        } 
        else 
        {
            return $query->leftJoin('mst_expenses_categories', 'trn_expenses.category_id', '=', 'mst_expenses_categories.id')->select('trn_expenses.*','mst_expenses_categories.name as category_name')->where('category_id',$category)->whereBetween('trn_expenses.created_at', [$drp_start, $drp_end])->orderBy($sorting_field,$sorting_direction);
        }
        
    }

}
