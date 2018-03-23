<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class SmsTrigger extends Model
{
    protected $table = 'mst_sms_triggers';

    protected $fillable = [
            'name',
            'alias',
            'message',
            'status',
            'updated_by',
    ];

    const CREATED_AT = null;

    //Eloquence Search mapping
    use Eloquence;

    protected $searchableColumns = [
        'name' => 20,
        'message' => 10,
    ];

    use createdByUser;

    use updatedByUser;
}
