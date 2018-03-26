<?php

namespace App;

trait createdByUser
{
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
