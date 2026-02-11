<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Enums\Status;
use Filament\Schemas\Schema;
use App\Helpers\Helpers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\RawJs;

class ExpenseForm
{
    /**
     * @return array<string, string>
     */
    public static function getStatusOptions(): array
    {
        return [
            Status::Pending->value => 'Pending',
            Status::Paid->value => 'Paid',
            Status::Overdue->value => 'Overdue',
            Status::Cancelled->value => 'Cancelled',
        ];
    }

    /**
     * Configure the expense form schema.
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
                    ->columns(6)
                    ->schema([
                        Group::make()
                            ->columns(6)
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Expense Name')
                                    ->placeholder('E.g. Electricity bill')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                Select::make('category')
                                    ->label('Category')
                                    ->options(fn (): array => Helpers::getExpenseCategoryOptions())
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(3),
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->prefix(Helpers::getCurrencySymbol())
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters([','])
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->columnSpan(2),
                                DatePicker::make('date')
                                    ->label('Date')
                                    ->default(fn(): string => now()->timezone(config('app.timezone'))->toDateString())
                                    ->required()
                                    ->columnSpan(2),
                                DatePicker::make('due_date')
                                    ->label('Due Date')
                                    ->columnSpan(2),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->placeholder('Optional notes…')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options(static::getStatusOptions())
                                    ->default(Status::Pending->value)
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                                        if ($state === Status::Paid->value) {
                                            if (blank($get('paid_at'))) {
                                                $set('paid_at', now()->timezone(config('app.timezone'))->format('Y-m-d H:i:s'));
                                            }

                                            return;
                                        }

                                        $set('paid_at', null);
                                    })
                                    ->required()
                                    ->columnSpan(2),
                                DateTimePicker::make('paid_at')
                                    ->label('Paid at')
                                    ->seconds(false)
                                    ->timezone(config('app.timezone'))
                                    ->visible(fn(Get $get): bool => $get('status') === Status::Paid->value)
                                    ->required(fn(Get $get): bool => $get('status') === Status::Paid->value)
                                    ->columnSpan(2),
                                TextInput::make('vendor')
                                    ->label('Vendor')
                                    ->placeholder('Vendor name')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                            ]),
                    ]),
            ]);
    }
}
