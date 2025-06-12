<?php

namespace App\Models;

use App\Helpers\Helpers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
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
    protected static $relations_to_cascade = ['subscription'];

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
        'occupation',
        'address',
        'country',
        'state',
        'city',
        'pincode',
        'source',
        'joining_for',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['dob' => 'date'];

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
     * Get the subscription for the member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscription()
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
     * Get the Filament form schema for the follow-up.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Grid::make()
                ->schema([
                    Section::make('')
                        ->schema([
                            FileUpload::make('photo')
                                ->imageEditor()
                                ->preserveFilenames()
                                ->maxSize(1024 * 1024 * 10)
                                ->disk('public')
                                ->directory('images')
                                ->image()
                                ->placeholder('Upload a logo (max 10MB)')
                                ->loadingIndicatorPosition('left')
                                ->panelAspectRatio('6:4')
                                ->panelLayout('integrated')
                                ->removeUploadedFileButtonPosition('right')
                                ->uploadButtonPosition('left')
                                ->uploadProgressIndicatorPosition('left'),
                        ])->columnSpan(1),
                    Section::make('')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            TextInput::make('code')
                                                ->placeholder('Code for the member')
                                                ->label('Member Code')
                                                ->required(),
                                            TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Name')
                                                ->columnSpan(2)
                                        ])->columns(3),
                                    Grid::make()
                                        ->schema([
                                            Select::make('gender')->options([
                                                'male' => 'Male',
                                                'female' => 'Female',
                                                'other' => 'Other',
                                            ])->default('male')
                                                ->selectablePlaceholder(false)
                                                ->required(),
                                            DatePicker::make('dob')
                                                ->native(false)
                                                ->required()
                                                ->label('Date of Birth')
                                                ->placeholder('01-01-2001')
                                                ->displayFormat('d-m-Y')
                                                ->suffixIcon('heroicon-m-calendar-days'),
                                            Select::make('occupation')
                                                ->options([
                                                    'student' => 'Student',
                                                    'housewife' => 'Housewife',
                                                    'self_employed' => 'Self Employed',
                                                    'professional' => 'Professional',
                                                    'freelancer' => 'Freelancer',
                                                    'others' => 'Others'
                                                ])
                                                ->default('student')
                                                ->selectablePlaceholder(false),
                                        ])->columns(3),
                                    TextInput::make('health_issue')
                                        ->label('Health Issues (if any)')
                                        ->maxLength(500)
                                        ->placeholder('Any health issues?')
                                        ->columnSpanFull(),
                                ])->columns(2)
                        ])->columnSpan(2),
                ])->columns(3),
            Section::make('Contact')
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->live()
                        ->maxLength(255)
                        ->required()
                        ->unique('members', 'email', ignoreRecord: true),
                    TextInput::make('contact')
                        ->tel()
                        ->placeholder('+1 555-123-4567')
                        ->maxLength(20)
                        ->regex('/^\+?[0-9\s\-\(\)]+$/') // Allows +, digits, spaces, dashes, and parentheses
                        ->required()
                        ->helperText('Include country code. Only digits, spaces, +, -, and () allowed.'),
                    TextInput::make('emergency_contact')
                        ->tel()
                        ->placeholder('+1 555-123-4567')
                        ->maxLength(20)
                        ->regex('/^\+?[0-9\s\-\(\)]+$/') // Allows +, digits, spaces, dashes, and parentheses
                        ->helperText('Include country code. Only digits, spaces, +, -, and () allowed.'),
                ])->columns(3)->columnSpanFull(),
            Section::make('Address')
                ->schema([
                    Group::make()
                        ->schema([
                            Textarea::make('address')
                                ->required()
                                ->placeholder('Room No./Wing, Building/Apt. name, street name'),
                            Group::make()
                                ->schema([
                                    Select::make('country')
                                        ->label('Country')
                                        ->placeholder('Select an country')
                                        ->options(Helpers::getCountries())
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn($state, callable $set) => [
                                            $set('state', null),
                                            $set('city', null),
                                        ]),
                                    Select::make('state')
                                        ->label('State')
                                        ->placeholder('Select an state')
                                        ->options(fn($get) => Helpers::getStates($get('country')))
                                        ->searchable()
                                        ->reactive(),
                                    Select::make('city')
                                        ->label('City')
                                        ->placeholder('Select an city')
                                        ->options(fn($get) => Helpers::getCities($get('state')))
                                        ->searchable()
                                        ->reactive(),
                                    TextInput::make('pincode')
                                        ->numeric()
                                        ->required()
                                        ->placeholder('PIN code'),
                                ])->columns(4),
                        ])->columnSpanFull(),
                ]),
            Section::make('Membership')
                ->schema([
                    Select::make('source')
                        ->options([
                            'promotions' => 'Promotions',
                            'word_of_mouth' => 'Word of mouth',
                            'others' => 'Others'
                        ])->default('promotions')
                        ->selectablePlaceholder(false),
                    Select::make('joining_for')
                        ->options([
                            'fitness' => 'Fitness',
                            'body_building' => 'Body Building',
                            'fatloss' => 'Fatloss',
                            'weightgain' => 'Weightgain',
                            'others' => 'Others'
                        ])->default('fitness')
                        ->label('Why do you plan to join?')
                        ->selectablePlaceholder(false),
                ])->columns(2)

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
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            ImageColumn::make('photo')
                ->circular()
                ->defaultImageUrl(fn(Member $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name)
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('member_code')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('name')
                ->searchable()
                ->label('Name'),
            TextColumn::make('email')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false)
                ->label('Email'),
            TextColumn::make('gender')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false)
                ->label('Gender'),
            TextColumn::make('contact')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Contact'),
            TextColumn::make('emergency_contact')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Emergency Contact'),
            TextColumn::make('created_at')
                ->sortable()
                ->date('d-m-Y')
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Date'),
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
