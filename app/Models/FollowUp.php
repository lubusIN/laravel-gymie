<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
{
    /** @use HasFactory<\Database\Factories\FollowUpFactory> */
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'enquiry_id',
        'follow_up_date',
        'follow_up_method',
        'outcome',
        'status'
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the enquiry for the follow-up.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }
}
