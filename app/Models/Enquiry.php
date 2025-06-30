<?php

namespace App\Models;

use App\Enums\Status;
use App\Helpers\Helpers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
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
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get the user for the enquiry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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

    /**
     * Get the Filament form schema for the enquiry.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Section::make('Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('John Doe'),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->live()
                        ->placeholder('user@example.com')
                        ->unique('enquiries', 'email', ignoreRecord: true),
                    TextInput::make('contact')->tel()->required()->placeholder('+91 555-123-4567'),
                    DatePicker::make('dob')
                        ->required()
                        ->label('Date of Birth'),
                    DatePicker::make('date')
                        ->default(now()),
                    Radio::make('gender')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                            'other' => 'Other',
                        ])
                        ->default('male')
                        ->inline()
                        ->inlineLabel(false)
                        ->required(),
                    Select::make('user_id')
                        ->label('Lead Owner')
                        ->placeholder('Select lead owner')
                        ->relationship('user', 'name')
                        ->required(),
                    DatePicker::make('start_by')
                        ->minDate(now())
                        ->placeholder(now()->format('d-m-Y')),
                ])->columns(3)->columnSpanFull(),
            Section::make('Location')
                ->schema([
                    Textarea::make('address')
                        ->required()
                        ->placeholder('Room No./Wing, Building/Apt. name, street name'),
                    Group::make()
                        ->schema([
                            Select::make('country')
                                ->label('Country')
                                ->placeholder('Select country')
                                ->options(Helpers::getCountries())
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn($state, callable $set) => [
                                    $set('state', null),
                                    $set('city', null),
                                ]),
                            Select::make('state')
                                ->label('State')
                                ->placeholder('Select state')
                                ->options(fn($get) => Helpers::getStates($get('country')))
                                ->searchable()
                                ->reactive(),
                            Select::make('city')
                                ->label('City')
                                ->placeholder('Select city')
                                ->options(fn($get) => Helpers::getCities($get('state')))
                                ->searchable()
                                ->reactive(),
                            TextInput::make('pincode')
                                ->numeric()
                                ->required()
                                ->placeholder('Enter PIN code'),
                        ])->columns(4),
                ]),
            Section::make('Choose your Preferences')
                ->schema([
                    Select::make('interested_in')
                        ->label('Interested In')
                        ->multiple()
                        ->placeholder('Select services')
                        ->options(fn() => Service::pluck('name', 'name')->toArray()),
                    Select::make('source')
                        ->options([
                            'promotions' => 'Promotions',
                            'word_of_mouth' => 'Word of mouth',
                            'others' => 'Others'
                        ])->default('promotions')
                        ->selectablePlaceholder(false),
                    Select::make('goal')
                        ->options([
                            'fitness' => 'Fitness',
                            'body_building' => 'Body Building',
                            'fatloss' => 'Fatloss',
                            'weightgain' => 'Weightgain',
                            'others' => 'Others'
                        ])->default('fitness')
                        ->label('Goal ?')
                        ->selectablePlaceholder(false),
                ])->columns(3),
            Section::make('Follow Details')
                ->schema([
                    Repeater::make('followUps')
                        ->relationship('followUps')
                        ->itemLabel('')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'new_enquiry_follow_up'])
                        ->schema([
                            Select::make('method')
                                ->options([
                                    'call' => 'Call',
                                    'email' => 'Email',
                                    'in_person' => 'In person',
                                    'whatsapp' => 'WhatsApp',
                                    'other' => 'Others'
                                ])->default('call')
                                ->required()
                                ->label('Follow-up method')
                                ->placeholder('Select follow up method'),
                            DatePicker::make('schedule_date')
                                ->label('Due Date')
                                ->required(),
                        ])
                        ->columns(2)
                        ->maxItems(1)
                        ->deletable(false)
                ])
                ->hiddenOn('edit'),
        ];
    }

    /**
     * Get the Filament table columns for the enquiry list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true)->label('ID'),
            TextColumn::make('name')->searchable()->sortable()->label('Name'),
            TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: false)->label('Email'),
            TextColumn::make('contact')->toggleable(isToggledHiddenByDefault: true)->label('Contact'),
            TextColumn::make('date')->sortable()->date('d-m-Y')->toggleable(isToggledHiddenByDefault: true)->label('Date'),
            TextColumn::make('start_by')->date('d-m-Y')->toggleable(isToggledHiddenByDefault: true)->label('Start by'),
            TextColumn::make('status')
                ->badge()
                ->label('Status')
                ->toggleable(isToggledHiddenByDefault: false),
        ];
    }
}
