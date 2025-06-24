<?php

namespace App\Models;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
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
        'user_id',
        'date',
        'due_date',
        'follow_up_method',
        'outcome',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
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

    /**
     * Get the user for the follow-up.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the Filament form schema for the follow-up.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Select::make('enquiry_id')
                ->label('Enquiry')
                ->relationship(name: 'enquiry', titleAttribute: 'name')
                ->placeholder('Select Enquiry')
                ->required(),
            Select::make('user_id')
                ->label('Handled By')
                ->relationship(name: 'user', titleAttribute: 'name')
                ->placeholder('Select Handler')
                ->required(),
            DatePicker::make('date')
                ->label('Date')
                ->closeOnDateSelection()
                ->default(now())
                ->disabledOn('edit')
                ->hiddenOn('create'),
            Select::make('follow_up_method')
                ->options([
                    'call' => 'Call',
                    'email' => 'Email',
                    'in_person' => 'In person',
                    'whatsapp' => 'WhatsApp',
                    'other' => 'Others'
                ])->default('call')
                ->required()
                ->label('Follow-up method'),
            DatePicker::make('due_date')
                ->label('Due Date')
                ->closeOnDateSelection()
                ->required()
                ->required()
                ->minDate(now()),
            Textarea::make('outcome')
                ->placeholder('Not interested, etc.')
                ->label('Outcome')
                ->required(),
        ];
    }

    /**
     * Get the Filament table columns for the follow-up list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('enquiry.name')
                ->searchable()
                ->label('Enquiry')
                ->weight(FontWeight::Bold)
                ->color('success')
                ->url(fn($record): string => route('filament.admin.resources.enquiries.view', $record->enquiry_id))
                ->sortable(),
            TextColumn::make('user.name')
                ->searchable()
                ->label('Handled By')
                ->weight(FontWeight::Bold)
                ->color('success')
                ->url(fn($record): string => route('filament.admin.resources.users.view', $record->user_id))
                ->sortable(),
            TextColumn::make('follow_up_method')
                ->label('Method')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('due_date')
                ->searchable()
                ->date('d-m-Y')
                ->label('Due Date')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('status')
                ->color(fn(string $state): string => match ($state) {
                    'done' => 'success',
                    'pending' => 'warning',
                })
                ->badge()
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'done' => 'Done',
                    'pending' => 'Pending',
                    default => ucfirst($state), // Fallback for any unexpected status
                })
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('outcome')
                ->toggleable(isToggledHiddenByDefault: true)
                ->limit(40)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();
                    if (strlen($state) <= $column->getCharacterLimit()) {
                        return null;
                    }
                    // Only render the tooltip if the column content exceeds the length limit.
                    return $state;
                }),
        ];
    }
}
