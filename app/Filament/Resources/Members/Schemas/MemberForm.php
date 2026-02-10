<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Filament\Resources\Subscriptions\Schemas\SubscriptionForm;
use App\Helpers\Helpers;
use App\Models\Member;
use Filament\Schemas\Schema;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;

class MemberForm
{
    /**
     * Configure the member form schema.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
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
                            ->schema(fn(HasSchemas $livewire): array => SubscriptionForm::configure(Schema::make($livewire))
                                ->getComponents(withActions: false)),
                    ]),
            ]);
    }
}
