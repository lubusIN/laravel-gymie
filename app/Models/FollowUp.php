<?php

namespace App\Models;

use App\Enums\Status;
use Database\Factories\FollowUpFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
{
    /** @use HasFactory<FollowUpFactory> */
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'enquiry_id',
        'user_id',
        'schedule_date',
        'method',
        'outcome',
        'status'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'status' => Status::class
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the enquiry for the follow-up.
     *
     * @return BelongsTo
     */
    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    /**
     * Get the user for the follow-up.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
