<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Status;
use App\Helpers\Helpers;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, SoftDeletes, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'photo',
        'name',
        'email',
        'status',
        'password',
        'contact',
        'dob',
        'gender',
        'address',
        'country',
        'city',
        'state',
        'pincode'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'status' => Status::class
        ];
    }

    protected $dates = ['deleted_at'];

    /**
     * Get the followUps for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Get the enquiries for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    /**
     * Get the URL for the user's Filament avatar.
     *
     * @return string|null The URL of the user's avatar or null if not set.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->photo ? asset($this->photo) : null;
    }

    /**
     * Determine if the user can access the Filament panel.
     *
     * @param Panel $panel The Filament panel instance.
     * @return bool True if the user can access the panel, false otherwise.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get the Filament form schema for the user.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Section::make()
                ->columns(4)
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
                        ->panelAspectRatio('6:5')
                        ->panelLayout('integrated')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('left')
                        ->uploadProgressIndicatorPosition('left'),
                    Grid::make()
                        ->columns(3)
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextInput::make('name')->required()->placeholder('e.g. John Doe'),
                                    TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->placeholder('user@example.com')
                                        ->unique(ignorable: fn($record) => $record)
                                        ->prefixIcon('heroicon-m-envelope'),
                                ])->columns(2)->columnSpanFull(),
                            TextInput::make('contact')
                                ->label('Contact')
                                ->prefixIcon('heroicon-m-phone')
                                ->tel()
                                ->placeholder('+91 555-123-4567')
                                ->maxLength(20)
                                ->regex('/^\+?[0-9\s\-\(\)]+$/') // Allows +, digits, spaces, dashes, and parentheses
                                ->required(),
                            Select::make('gender')
                                ->options([
                                    'male' => 'Male',
                                    'female' => 'Female',
                                    'other' => 'Other'
                                ])
                                ->required()
                                ->default('male')
                                ->selectablePlaceholder(false),
                            DatePicker::make('dob')
                                ->required()
                                ->label('Date of Birth'),
                            Select::make('role')
                                ->label('Role')
                                ->relationship('roles', 'name')
                                ->getOptionLabelFromRecordUsing(
                                    fn($record): string =>
                                    Str::headline($record->name)
                                ),
                            TextInput::make('password')
                                ->password()
                                ->hiddenOn(['view'])
                                ->dehydrated(fn($state) => filled($state))
                                ->required(fn(string $operation): bool => $operation === 'create')
                                ->revealable(),
                            TextInput::make('password_confirmation')
                                ->password()
                                ->hiddenOn(['view'])
                                ->revealable()
                                ->required(fn(callable $get): bool => filled($get('password')))
                                ->same('password'),
                        ])
                        ->columnSpan(3),
                ]),
            Section::make('Location')
                ->schema([
                    Textarea::make('address')
                        ->required()
                        ->placeholder('100/B, Oak Ave Apt. 10, Rass Street'),
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
                                ->afterStateUpdated(fn(callable $set) => [
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
                                ->placeholder('PIN Code'),
                        ])->columns(4),
                ]),
        ];
    }

    /**
     * Get the Filament table columns for the user list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ImageColumn::make('photo')
                ->circular()
                ->defaultImageUrl(fn(User $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name),
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('contact')->searchable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('gender')->searchable(),
            TextColumn::make('roles.name')
                ->placeholder('N/A')
                ->searchable()
                ->formatStateUsing(
                    fn($state): string =>
                    Str::headline($state)
                )
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('status')
                ->badge()
        ];
    }
}
