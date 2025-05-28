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
        'dob' => 'date',
        'start_by' => 'date',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the Filament form schema for the estimate.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return[
            Section::make('')
            ->schema([
                TextInput::make('name')->required()->maxLength(255)->placeholder('Name'),
                TextInput::make('email')->email()->required()->placeholder('user@example.com'),
                TextInput::make('contact')->tel()->required()->placeholder('+91 555-123-4567'),
                Select::make('gender')->options([
                    'male' => 'Male',
                    'female' => 'Female',
                    'others' => 'Others',
                ])->searchable()
                ->required(),
                DatePicker::make('dob')->native(false)
                    ->label('Date of Birth')
                    ->placeholder('01-01-2001')
                    ->suffixIcon('heroicon-m-calendar-days'),
                TextInput::make('occupation')->maxLength(255)->placeholder('e.g. student, accountant, etc.'),
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
                                    ->placeholder('PIN code'),
                            ])->columns(4),
                    ])->columnSpanFull(),
                TextInput::make('interested_in')
                    ->placeholder('Yoga, cardio, fatloss, etc.')
                    ->required(),
                Select::make('source')
                    ->options([
                        'promotions' => 'Promotions',
                        'word_of_mouth' => 'Word of mouth',
                        'others' => 'Others'
                    ])->default('promotions')
                    ->required(),
                Select::make('why_do_you_plan_to_join')
                    ->options([
                        'fitness' => 'Fitness',
                        'networking' => 'Networking',
                        'body_building' => 'Body Building',
                        'fatloss' => 'Fatloss',
                        'weightlgain' => 'Weightgain',
                        'others' => 'Others'
                    ])->default('fitness')
                    ->required(),
                DatePicker::make('start_by')
                    ->native(false)
                    ->required()
                    ->placeholder(now()->format('d-m-Y'))
                    ->suffixIcon('heroicon-m-calendar-days'),
            ])->columns(3)

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
            TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('contact')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('status')->colors([
                'info' => 'lead',
                'success' => 'member',
                'danger' => 'lost',
            ])->badge()
            ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('gender')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('address')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('country')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('state')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('city')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('start_by')->date()->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

}
