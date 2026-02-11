<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Enums\Status;
use App\Filament\Resources\Expenses\Schemas\ExpenseInfolist;
use App\Helpers\Helpers;
use App\Models\Expense;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpenseTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Expense')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(decimalPlaces: 2)
                    ->prefix(Helpers::getCurrencySymbol())
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Category')
                    ->searchable()
                    ->formatStateUsing(fn(?string $state): string => Helpers::getExpenseCategoryLabel($state) ?? 'N/A'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(Status $state): string => $state->getColor()),
                TextColumn::make('vendor')
                    ->label('Vendor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paid_at')
                    ->label('Paid at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modal()
                        ->url(null)
                        ->modalCancelAction(false)
                        ->modalAlignment('center')
                        ->modalWidth(Width::ScreenSmall)
                        ->schema(fn($livewire, Expense $record): array => ExpenseInfolist::configure(
                            Schema::make($livewire)->model($record)->record($record),
                        )->getComponents(withActions: false)),
                    EditAction::make()
                        ->modalHeading('Edit Expense')
                        ->modalSubmitActionLabel('Save')
                        ->modalWidth(Width::ScreenLarge)
                        ->closeModalByClickingAway(false),
                    DeleteAction::make(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->dropdown(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateHeading('No Expenses')
            ->emptyStateDescription('Create an expense to get started.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Add expense')
                    ->icon('heroicon-m-plus')
                    ->modalHeading('Add Expense')
                    ->modalSubmitActionLabel('Save')
                    ->createAnother()
                    ->createAnotherAction(fn($action) => $action->label('Save & add another'))
                    ->modalWidth(Width::ScreenLarge)
                    ->closeModalByClickingAway(false)
                    ->visible(fn() => !Expense::exists()),
            ]);
    }
}
