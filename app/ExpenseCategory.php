<?php

namespace App;

use Lubus\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'mst_expenses_categories';

    protected $fillable = [
            'name',
            'total_expense',
            'status',
            'created_by',
            'updated_by',
    ];

    public function scopeExcludeArchive($query)
    {
        return $query->where('status', '!=', \constStatus::Archive);
    }

    public function Expenses()
    {
        return $this->hasMany('App\Expense', 'category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('app\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('app\User', 'updated_by');
    }
}
