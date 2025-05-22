<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
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
                Forms\Components\FileUpload::make('avatar')
                    ->hiddenLabel()
                    ->placeholder('Upload a avatar (max 10MB)')
                    ->avatar()
                    ->imageEditor()
                    ->preserveFilenames()
                    ->maxSize(1024 * 1024 * 10)
                    ->disk('public')
                    ->directory('images')
                    ->image()
                    ->extraAttributes(['class' => 'cursor-pointer'])
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required()->unique(ignorable: fn($record) => $record)
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
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required()
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
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->defaultImageUrl(fn(User $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(fn(User $record): string => 'View ' . $record->name)
                    ->hiddenLabel()
                    ->infolist([
                        ImageEntry::make('avatar')
                            ->hiddenLabel()
                            ->height(100)
                            ->circular()
                            ->columnSpanFull()
                            ->defaultImageUrl(fn(User $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name),
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('status')
                            ])
                    ])->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Tables\Actions\EditAction::make()
                    ->hiddenLabel()
                    ->modalHeading('Edit User'),
                Tables\Actions\DeleteAction::make()
                    ->hiddenLabel(),
            ])
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
        ];
    }
}
