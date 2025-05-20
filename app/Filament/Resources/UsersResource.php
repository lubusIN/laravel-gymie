<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
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
            ]);
    }

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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/')
        ];
    }
}
