<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;

class Service extends Model
{
    //Eloquence Search mapping
    use Eloquence;
    use createdByUser, updatedByUser;

    protected $table = 'mst_services';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $searchableColumns = [
        'name' => 20,
        'description' => 10,
    ];

    public function plans()
    {
        return $this->hasMany('App\Plan', 'service_id');
    }
}
