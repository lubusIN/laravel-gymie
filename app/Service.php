<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class Service extends Model
{
    //Eloquence Search
    use SearchableTrait;
    use createdByUser, updatedByUser;

    protected $table = 'mst_services';

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $searchable = [
        'columns' => [
            'name' => 10,
            'description' => 5
        ]
    ];

    public function plans()
    {
        return $this->hasMany('App\Plan', 'service_id');
    }
}
