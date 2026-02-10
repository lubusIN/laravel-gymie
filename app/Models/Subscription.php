<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'member_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'status'     => Status::class
    ];

    protected $dates = ['deleted_at', 'start_date', 'end_date'];

    /**
     * Get the invoices for the subscription.
     *
     * @return hasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * The member who owns this subscription.
     *
     * @return BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * The plan this subscription is for.
     *
     * @return BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
