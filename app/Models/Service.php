<?php

namespace App\Models;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Holds the methods' names of Eloquent Relations
     * to fall on delete cascade or on restoring
     *
     * @var string[]
     */
    protected static $relations_to_cascade = ['plans'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the plans for the service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function plans()
    {
        return $this->hasMany(Plan::class);
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

    /**
     * Get the Filament form schema for the services.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Name')
                ->placeholder('Service name')
                ->required(),
            Textarea::make('description')
                ->placeholder('Brief description of the service')
                ->label('Description')
                ->required(),
        ];
    }

    /**
     * Get the Filament table columns for the services list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('name')
                ->searchable()
                ->label('Name')
                ->sortable(),
            TextColumn::make('description')
                ->searchable()
                ->label('Description'),
            TextColumn::make('created_at')
                ->searchable()
                ->date('d-m-Y')
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
