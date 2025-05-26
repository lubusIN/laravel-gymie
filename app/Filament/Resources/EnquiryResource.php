<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnquiryResource\Pages;
use App\Filament\Resources\EnquiryResource\RelationManagers;
use App\Helpers\Helpers;
use App\Models\Enquiry;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make('details')
                    ->heading('Enter details of the enquiry')
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('email')->email()->required(),
                        TextInput::make('contact')->tel()->required(),
                        Select::make('gender')->options([
                            'male' => 'Male',
                            'female' => 'Female',
                            'others' => 'Others',
                        ])->searchable(),
                        DatePicker::make('dob')->native(false),
                        TextInput::make('occupation')->maxLength(255),
                        Group::make()
                            ->schema([
                                Textarea::make('address')
                                    ->required(),
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
                                            ->numeric(),
                                    ])->columns(4),
                            ])->columnSpanFull(),
                        TextInput::make('interested_in'),
                        TextInput::make('source'),
                        TextInput::make('why_do_you_plan_to_join'),
                        DatePicker::make('start_by')->native(false),
                    ])->columns(3)

            ]);
    }

    public static function table(Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('contact'),
                TextColumn::make('status')->colors([
                    'primary' => 'lead',
                    'success' => 'member',
                    'danger' => 'lost',
                ]),
                TextColumn::make('gender'),
                TextColumn::make('city'),
                TextColumn::make('start_by')->date(),
                TextColumn::make('created_at')->dateTime()->label('Enquired On'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])->recordUrl(fn($record): string => route('filament.admin.resources.enquiries.view', $record->id))
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
            'index' => Pages\ListEnquiries::route('/'),
            'create' => Pages\CreateEnquiry::route('/create'),
            'edit' => Pages\EditEnquiry::route('/{record}/edit'),
            'view' => Pages\ViewEnquiry::route('/{record}'),
        ];
    }
}
