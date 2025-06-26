<?php

namespace App\Models;

use App\Enums\Status;
use App\Filament\Resources\EnquiryResource\RelationManagers\FollowUpsRelationManager;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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
                ->hiddenOn(FollowUpsRelationManager::class)
                ->required(),
            Select::make('method')
                ->options([
                    'call' => 'Call',
                    'email' => 'Email',
                    'in_person' => 'In person',
                    'whatsapp' => 'WhatsApp',
                    'other' => 'Others'
                ])->default('call')
                ->required()
                ->label('Method'),
            DatePicker::make('schedule_date')
                ->label('Schedule Date')
                ->closeOnDateSelection()
                ->required()
                ->required()
                ->minDate(now()),
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
                ->sortable(),
            TextColumn::make('user.name')
                ->searchable()
                ->label('Handled By')
                ->placeholder('N/A')
                ->sortable(),
            TextColumn::make('method')
                ->label('Method')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('schedule_date')
                ->searchable()
                ->date('d-m-Y')
                ->label('Schedule Date')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('status')
                ->badge()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('outcome')
                ->toggleable(isToggledHiddenByDefault: true)
                ->placeholder('N/A')
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
