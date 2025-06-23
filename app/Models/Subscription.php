<?php

namespace App\Models;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
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
    ];

    protected $dates = ['deleted_at', 'start_date', 'end_date'];

    /**
     * Get the invoices for the subscription.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * The member who owns this subscription.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * The plan this subscription is for.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the Filament form schema for the subscription.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Select::make('member_id')
                ->relationship('member', 'name')
                ->native(false)
                ->searchable()
                ->preload()
                ->placeholder('Select a member')
                ->getOptionLabelFromRecordUsing(fn(Member $record): string => "{$record->code} - {$record->name}")
                ->required(),
            Select::make('plan_id')
                ->relationship('plan', 'name')
                ->native(false)
                ->searchable()
                ->preload()
                ->placeholder('Select a plan')
                ->getOptionLabelFromRecordUsing(fn(Plan $record): string => "{$record->code} - {$record->name}")
                ->required(),
            DatePicker::make('start_date')
                ->label('Start Date')
                ->live()
                ->native(false)
                ->required()
                ->placeholder('01-01-2001')
                ->displayFormat('d-m-Y')
                ->suffixIcon('heroicon-m-calendar-days')
                ->before('end_date'),
            DatePicker::make('end_date')
                ->label('End Date')
                ->live()
                ->native(false)
                ->required()
                ->placeholder('01-01-2001')
                ->displayFormat('d-m-Y')
                ->suffixIcon('heroicon-m-calendar-days')
                ->after('start_date'),
        ];
    }

    /**
     * Get the Filament table columns for the subscription list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('member.name')
                ->label('Member')
                ->description(fn($record): string => $record->member->code)
                ->weight(FontWeight::Bold)
                ->color('success')
                ->url(fn($record): string => route('filament.admin.resources.members.view', $record->member_id)),
            TextColumn::make('plan.name')
                ->label('Plan')
                ->description(fn($record): string => $record->plan->code)
                ->weight(FontWeight::Bold)
                ->color('success')
                ->url(fn($record): string => route('filament.admin.resources.plans.view', $record->plan_id)),
            TextColumn::make('start_date')
                ->label('Start Date')
                ->date(),
            TextColumn::make('end_date')
                ->label('End Date')
                ->date(),
            TextColumn::make('created_at')
                ->date()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('status')
                ->badge()
                ->color(fn(string $state): string => match ($state) {
                    'ongoing' => 'success',
                    'expiring' => 'warning',
                    'expired' => 'danger',
                })
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'ongoing' => 'Ongoing',
                    'expiring' => 'Expiring',
                    'expired' => 'Expired',
                    default => ucfirst($state),
                }),
        ];
    }
}
