<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\Status;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

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
        'photo',
        'code',
        'name',
        'email',
        'contact',
        'emergency_contact',
        'health_issue',
        'gender',
        'dob',
        'address',
        'country',
        'state',
        'city',
        'pincode',
        'source',
        'goal',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['dob' => 'date', 'status' => Status::class];

    /**
     * The attributes that should be mutated to dates.
     * (SoftDeletes already adds deleted_at rollover.)
     *
     * @var array
     */
    protected $dates = [
        'dob',
        'deleted_at',
    ];

    /**
     * Get the subscriptions for the member.
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

        static::saving(function ($member) {
            if (!$member->code) {
                $member->code = Helpers::generateLastNumber('member', Member::class, null, 'code');
            }
            Helpers::updateLastNumber('member', $member->code);
        });

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
