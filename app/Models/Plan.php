<?php

namespace App\Models;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'service',
        'amount',
        'days',
        'status',
    ];

    protected $casts = [
        'service' => 'array',
    ];

    protected $dates = ['deleted_at'];

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
                        ->unique(ignoreRecord:true)
                        ->required(),
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder('Name of the plan')
                        ->unique(ignoreRecord:true)
                        ->required(),
                    TextInput::make('description')
                        ->placeholder('Brief description of the plan')
                        ->label('Description')
                        ->required(),
                    Select::make('service')
                        ->label('Service')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn() => Service::pluck('name', 'name')),
                    TextInput::make('days')
                        ->required()
                        ->placeholder('Number of days for the plan')
                        ->numeric()
                        ->label('Days'),
                    TextInput::make('amount')
                        ->placeholder('Enter amount of the plan')
                        ->numeric()
                        ->prefixIcon('heroicon-o-currency-rupee')
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
                ->label('code')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('name')
                ->searchable()
                ->label('Name')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('description')
                ->searchable()
                ->label('Description')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('service')
                ->searchable()
                ->label('Service')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('days')
                ->searchable()
                ->label('Days')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('amount')
                ->searchable()
                ->label('Amount')
                ->toggleable(isToggledHiddenByDefault: true),
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
