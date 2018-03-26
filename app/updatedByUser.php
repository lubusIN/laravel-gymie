<?php

namespace App;

trait updatedByUser
{
    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
