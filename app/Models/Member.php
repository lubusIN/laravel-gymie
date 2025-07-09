<?php

namespace App\Models;

use App\Enums\Status;
use App\Helpers\Helpers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
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

    /**
     * Get the Filament form schema for the follow-up.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [

            Section::make()
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
                        ->panelAspectRatio('6:7')
                        ->panelLayout('integrated')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('left')
                        ->uploadProgressIndicatorPosition('left'),

                    Grid::make()
                        ->schema([
                            TextInput::make('code')
                                ->placeholder('Code for the member')
                                ->label('Member Code')
                                ->required()
                                ->readOnly()
                                ->disabled()
                                ->dehydrated()
                                ->default(fn(Get $get) => Helpers::generateLastNumber(
                                    'member',
                                    Member::class,
                                    null,
                                    'code'
                                )),
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Name')
                                ->columnSpan(2),
                            TextInput::make('email')
                                ->email()
                                ->live()
                                ->maxLength(255)
                                ->required()
                                ->placeholder('Email address')
                                ->unique('members', 'email', ignoreRecord: true),
                            TextInput::make('contact')
                                ->tel()
                                ->placeholder('+1 555-123-4567')
                                ->maxLength(20)
                                ->regex('/^\+?[0-9\s\-\(\)]+$/') // Allows +, digits, spaces, dashes, and parentheses
                                ->required()
                                ->hintIcon('heroicon-m-question-mark-circle')
                                ->hintIconTooltip('Include country code. Only digits, spaces, +, -, and () allowed.'),
                            TextInput::make('emergency_contact')
                                ->tel()
                                ->placeholder('+1 555-123-4567')
                                ->maxLength(20)
                                ->regex('/^\+?[0-9\s\-\(\)]+$/') // Allows +, digits, spaces, dashes, and parentheses
                                ->hintIcon('heroicon-m-question-mark-circle')
                                ->hintIconTooltip('Include country code. Only digits, spaces, +, -, and () allowed.'),
                            Select::make('gender')
                                ->options([
                                    'male' => 'Male',
                                    'female' => 'Female',
                                    'other' => 'Other',
                                ])->default('male')
                                ->selectablePlaceholder(false)
                                ->required(),
                            DatePicker::make('dob')
                                ->required()
                                ->label('Date of Birth')
                                ->placeholder('01-01-2001'),
                            TextInput::make('health_issue')
                                ->label('Health Issues (if any)')
                                ->maxLength(500)
                                ->placeholder('Any health issues?'),
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
                        ])->columns(3)->columnSpan(3)
                ])->columns(4),
            Section::make('Location')
                ->columns(2)
                ->schema([
                    Textarea::make('address')
                        ->required()
                        ->rows(5)
                        ->placeholder('Room No./Wing, Building/Apt. name, street name'),
                    Group::make()
                        ->columns(2)
                        ->schema([
                            Select::make('country')
                                ->label('Country')
                                ->placeholder('Select an country')
                                ->options(Helpers::getCountries())
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
                                ->reactive(),
                            Select::make('city')
                                ->label('City')
                                ->placeholder('Select an city')
                                ->options(fn($get) => Helpers::getCities($get('state')))
                                ->reactive(),
                            TextInput::make('pincode')
                                ->numeric()
                                ->required()
                                ->placeholder('PIN code'),
                        ]),
                ]),
            Section::make('Subscription & Invoice')
                ->visibleOn('create')
                ->schema([
                    Repeater::make('subscriptions')
                        ->relationship('subscriptions')
                        ->itemLabel('')
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->maxItems(1)
                        ->deletable(false)
                        ->extraAttributes(['class' => 'rmv_rept-space'])
                        ->columns(3)
                        ->schema(Subscription::getForm()),
                ]),
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
                ->defaultImageUrl(fn(Member $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name),
            TextColumn::make('code')
                ->searchable(),
            TextColumn::make('name')
                ->searchable()
                ->label('Name'),
            TextColumn::make('email')
                ->searchable()
                ->label('Email'),
            TextColumn::make('gender')
                ->searchable()
                ->label('Gender'),
            TextColumn::make('contact')
                ->searchable()
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
                ->badge()
                ->label('Status'),
        ];
    }
}
