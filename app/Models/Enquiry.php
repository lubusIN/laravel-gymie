<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Holds the methods' names of Eloquent Relations
     * to fall on delete cascade or on restoring
     *
     * @var string[]
     */
    protected static $relations_to_cascade = ['followUps'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'contact',
        'date',
        'gender',
        'dob',
        'status',
        'address',
        'country',
        'city',
        'state',
        'pincode',
        'interested_in',
        'source',
        'goal',
        'start_by'
    ];

    protected $casts = [
        'interested_in' => 'array',
        'date'          => 'date',
        'dob'           => 'date',
        'start_by'      => 'date',
        'status'        => Status::class
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the followUps for the enquiry.
     *
     * @return hasMany
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get the user for the enquiry.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
