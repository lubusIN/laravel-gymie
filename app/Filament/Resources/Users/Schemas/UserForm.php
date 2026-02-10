<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Helpers\Helpers;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

class UserForm
{
    /**
     * Configure the user form schema.
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
            ]);
    }
}
