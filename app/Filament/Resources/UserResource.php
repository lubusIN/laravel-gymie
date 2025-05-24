<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Helpers\Helpers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->columns(3)
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->hiddenLabel()
                                    ->placeholder('Upload the profile photo (max 10MB)')
                                    ->avatar()
                                    ->imageEditor()
                                    ->preserveFilenames()
                                    ->maxSize(1024 * 1024 * 10)
                                    ->disk('public')
                                    ->directory('images')
                                    ->image()
                                    ->extraAttributes(['class' => 'cursor-pointer'])
                                    ->columnSpan(1),
                                Forms\Components\Grid::make()
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')->required(),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique(ignorable: fn($record) => $record)
                                            ->prefixIcon('heroicon-m-envelope'),
                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->hiddenOn('view')
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->revealable(),
                                        Forms\Components\TextInput::make('password_confirmation')
                                            ->password()
                                            ->hiddenOn('view')
                                            ->revealable()
                                            ->required(fn(callable $get): bool => filled($get('password')))
                                            ->same('password'),
                                    ])
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('contact')
                                    ->label('Contact')
                                    ->prefixIcon('heroicon-m-phone')
                                    ->tel()
                                    ->placeholder('+91 555-123-4567')
                                    ->maxLength(20)
                                    ->regex('/^\+?[0-9\s\-\(\)]+$/') // Allows +, digits, spaces, dashes, and parentheses
                                    ->required(),
                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'none' => 'None',
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ])
                                    ->default('none')
                                    ->selectablePlaceholder(false)
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        '' => 'No Status',
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->required()
                                    ->selectablePlaceholder(false)
                            ]),
                    ]),
                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->required(),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('country')
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
                                Forms\Components\Select::make('state')
                                    ->label('State')
                                    ->placeholder('Select an state')
                                    ->options(fn($get) => Helpers::getStates($get('country')))
                                    ->searchable()
                                    ->reactive(),
                                Forms\Components\Select::make('city')
                                    ->label('City')
                                    ->placeholder('Select an city')
                                    ->options(fn($get) => Helpers::getCities($get('state')))
                                    ->searchable()
                                    ->reactive(),
                                Forms\Components\TextInput::make('pincode')
                                    ->numeric(),
                            ])->columns(4),
                    ]),



            ]);
    }

    /**
     * Define the table for listing records in the resource.
     *
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->defaultImageUrl(fn(User $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('contact')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gender')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('state')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('city')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pincode')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->hiddenLabel(),
                Tables\Actions\DeleteAction::make()->hiddenLabel(),
                Tables\Actions\RestoreAction::make()
            ])->recordUrl(fn($record): string => route('filament.admin.resources.users.view', $record->id))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
