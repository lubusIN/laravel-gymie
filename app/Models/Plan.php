<?php

namespace App\Models;

use App\Helpers\Helpers;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
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

    protected $dates = ['deleted_at'];

    /**
     * Get the sevice for the plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the subscriptions for the plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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

    /**
     * Get the Filament form schema for the plans.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Section::make('')
                ->schema([
                    TextInput::make('code')
                        ->placeholder('Code for the plan')
                        ->label('Code')
                        ->unique(ignoreRecord: true)
                        ->required(),
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder('Name of the plan')
                        ->unique(ignoreRecord: true,)
                        ->required(),
                    Select::make('service_id')
                        ->label('Service')
                        ->relationship(name: 'service', titleAttribute: 'name')
                        ->placeholder('Select service')
                        ->required()
                        ->searchable()
                        ->preload(),
                    TextInput::make('description')
                        ->placeholder('Brief description of the plan')
                        ->label('Description'),
                    TextInput::make('days')
                        ->required()
                        ->placeholder('Number of days for the plan')
                        ->numeric()
                        ->label('Days'),
                    TextInput::make('amount')
                        ->placeholder('Enter amount of the plan')
                        ->numeric()
                        ->prefix(Helpers::getCurrencySymbol())
                        ->label('Amount')
                        ->required(),
                ])->columns(2)
        ];
    }

    /**
     * Get the Filament table columns for the plans list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('code')
                ->searchable()
                ->label('Code')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('name')
                ->searchable()
                ->label('Name')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('description')
                ->searchable()
                ->label('Description')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('service.name')
                ->searchable()
                ->label('Service')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('days')
                ->searchable()
                ->label('Days')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('amount')
                ->searchable()
                ->label('Amount')
                ->money(Helpers::getCurrencyCode())
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('status')
                ->color(fn(string $state): string => match ($state) {
                    'active' => 'success',
                    'inactive' => 'danger',
                })->badge()
                ->label('Status')
                ->toggleable(isToggledHiddenByDefault: false)
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    default => ucfirst($state), // Fallback for any unexpected status
                }),
        ];
    }
}
