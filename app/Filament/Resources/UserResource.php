<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Helpers\Helpers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

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
                Forms\Components\Section::make('')
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
                                        Forms\Components\TextInput::make('name')->required()->placeholder('e.g. John Doe'),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->placeholder('user@example.com')
                                            ->unique(ignorable: fn($record) => $record)
                                            ->prefixIcon('heroicon-m-envelope'),
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
                                                'male' => 'Male',
                                                'female' => 'Female',
                                                'other' => 'Other'
                                            ])
                                            ->required()
                                            ->default('male')
                                            ->selectablePlaceholder(false),
                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->hiddenOn(['view', 'edit'])
                                            ->dehydrated(fn($state) => filled($state))
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->revealable(),
                                        Forms\Components\TextInput::make('password_confirmation')
                                            ->password()
                                            ->hiddenOn(['view', 'edit'])
                                            ->revealable()
                                            ->required(fn(callable $get): bool => filled($get('password')))
                                            ->same('password'),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ]),
                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->placeholder('100/B, Oak Ave Apt. 10, Rass Street'),
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
                                    ->afterStateUpdated(fn(callable $set) => [
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
                                    ->numeric()
                                    ->placeholder('PIN Code'),
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
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('inactive')
                        ->label('Mark as Inactive')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->icon('heroicon-s-x-circle')
                        ->action(fn(User $record) => tap($record, function ($record) {
                            $record->update(['status' => 'inactive']);
                            Notification::make()
                                ->title('Inactive')
                                ->danger()
                                ->body("{$record->name} has been inactivated.")
                                ->send();
                        }))
                        ->visible(fn($record) => $record->status === 'active'),
                    Tables\Actions\Action::make('active')
                        ->label('Mark as Active')
                        ->color('success')
                        ->requiresConfirmation()
                        ->icon('heroicon-s-check-circle')
                        ->action(fn(User $record) => tap($record, function ($record) {
                            $record->update(['status' => 'active']);
                            Notification::make()
                                ->title('Active')
                                ->success()
                                ->body("{$record->name} has been activated.")
                                ->send();
                        }))
                        ->visible(fn($record) => $record->status === 'inactive'),
                    Tables\Actions\EditAction::make()->hiddenLabel(),
                    Tables\Actions\DeleteAction::make()->hiddenLabel(),
                    Tables\Actions\RestoreAction::make()
                ])
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
