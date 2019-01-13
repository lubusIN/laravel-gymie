<?php

namespace App;

use Lubus\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use createdByUser, updatedByUser;

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

    public function expenses()
    {
        return $this->hasMany('App\Expense', 'category_id');
    }
}
