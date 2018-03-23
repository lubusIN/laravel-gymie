<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'mst_services';

    protected $fillable = [
            'name',
            'description',
            'created_by',
            'updated_by',
    ];

    //Eloquence Search mapping
    use Eloquence;

    protected $searchableColumns = [
        'name' => 20,
        'description' => 10,
    ];

    use createdByUser;

    use updatedByUser;

    public function Plans()
    {
        return $this->hasMany('App\Plan', 'service_id');
    }
}
