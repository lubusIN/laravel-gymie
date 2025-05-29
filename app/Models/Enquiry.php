<?php

namespace App\Models;

use App\Helpers\Helpers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Enquiry extends Model
{
    use SoftDeletes, HasFactory, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact',
        'date',
        'gender',
        'dob',
        'occupation',
        'status',
        'address',
        'country',
        'city',
        'state',
        'pincode',
        'interested_in',
        'source',
        'why_do_you_plan_to_join',
        'start_by'
    ];

    protected $casts = [
        'date' => 'date',
        'dob' => 'date',
        'start_by' => 'date',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the enquiry for the follow-up.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function follow_up()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get the Filament form schema for the enquiry.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Section::make('')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255)->placeholder('Name'),
                    TextInput::make('email')->email()->required()->placeholder('user@example.com'),
                    TextInput::make('contact')->tel()->required()->placeholder('+91 555-123-4567'),
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
                        ->selectablePlaceholder(false)
                        ->required(),
                    DatePicker::make('date')
                        ->default(now())
                        ->suffixIcon('heroicon-m-calendar-days')
                        ->displayFormat('d-m-Y')
                        ->native(false),
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
            Section::make('Choose your Preferences')
                ->schema([
                    Select::make('interested_in')
                        ->options([
                            'beginner_pkg' => 'Beginner PKG',
                            'personal_trainer' => 'Personal Trainer',
                            'gym' => 'Gym',
                            'yoga' => 'Yoga',
                            'fatloss' => 'Fatloss',
                            'others' => 'Others'
                        ])
                        ->default('beginner_pkg')
                        ->selectablePlaceholder(false)
                        ->required(),
                    Select::make('source')
                        ->options([
                            'promotions' => 'Promotions',
                            'word_of_mouth' => 'Word of mouth',
                            'others' => 'Others'
                        ])->default('promotions')
                        ->selectablePlaceholder(false)
                        ->required(),
                    Select::make('why_do_you_plan_to_join')
                        ->options([
                            'fitness' => 'Fitness',
                            'body_building' => 'Body Building',
                            'fatloss' => 'Fatloss',
                            'weightgain' => 'Weightgain',
                            'others' => 'Others'
                        ])->default('fitness')
                        ->label('Why do you plan to join?')
                        ->selectablePlaceholder(false)
                        ->required(),
                    DatePicker::make('start_by')
                        ->native(false)
                        ->required()
                        ->minDate(now())
                        ->displayFormat('d-m-Y')
                        ->placeholder(now()->format('d-m-Y'))
                        ->suffixIcon('heroicon-m-calendar-days'),
                ])->columns(2)

        ];
    }

    /**
     * Get the Filament table columns for the estimate list view.
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
                ->color(fn(string $state): string => match ($state) {
                    'lead' => 'info',
                    'member' => 'success',
                    'lost' => 'danger',
                })->badge()
                ->label('Status')
                ->toggleable(isToggledHiddenByDefault: false)
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'lead' => 'Lead',
                    'member' => 'Member',
                    'lost' => 'Lost',
                    default => ucfirst($state), // Fallback for any unexpected status
                }),
        ];
    }
}
