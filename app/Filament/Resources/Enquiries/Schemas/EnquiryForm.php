<?php

namespace App\Filament\Resources\Enquiries\Schemas;

use App\Helpers\Helpers;
use App\Models\Service;
use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Blade;

class EnquiryForm
{
    /**
     * Configure the enquiry form schema.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
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
                            ->required()
                            ->getOptionLabelFromRecordUsing(function (User $record): string {
                                $name = html_entity_decode($record->name, ENT_QUOTES, 'UTF-8');
                                $url  = !empty($record->photo) ? e($record->photo) : "https://ui-avatars.com/api/?background=000&color=fff&name={$name}";
                                return Blade::render(
                                    '<div class="flex items-center gap-2 h-9">
                                    <x-filament::avatar src="{{ $url }}" alt="{{ $name }}" size="sm" />
                                    <span class="ml-2">{{ $name }}</span>
                                 </div>',
                                    compact('url', 'name')
                                );
                            })
                            ->allowHtml(),
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
            ]);
    }
}
