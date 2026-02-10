<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Holds the methods' names of Eloquent Relations
     * to fall on delete cascade or on restoring
     *
     * @var string[]
     */
    protected static $relations_to_cascade = ['subscriptions'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'service_id',
        'amount',
        'days',
        'status',
    ];

    protected $casts = [
        'status' => Status::class
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the sevice for the plan.
     *
     * @return BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the subscriptions for the plan.
     *
     * @return HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Boot the model and add cascade delete and restore behavior.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($resource) {
            foreach (static::$relations_to_cascade as $relation) {
                foreach ($resource->{$relation}()->get() as $item) {
                    $item->delete();
                }
            }
        });

        static::restoring(function ($resource) {
            foreach (static::$relations_to_cascade as $relation) {
                foreach ($resource->{$relation}()->withTrashed()->get() as $item) {
                    $item->restore();
                }
            }
        });
    }
}
