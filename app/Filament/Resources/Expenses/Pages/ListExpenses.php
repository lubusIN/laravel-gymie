<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Enums\Status;
use App\Filament\Resources\Expenses\ExpenseResource;
use App\Models\Expense;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add expense')
                ->icon('heroicon-m-plus')
                ->modalHeading('Add Expense')
                ->modalSubmitActionLabel('Save')
                ->createAnother()
                ->createAnotherAction(fn($action) => $action->label('Save & add another'))
                ->modalWidth(Width::ScreenLarge)
                ->closeModalByClickingAway(false)
                ->hidden(!Expense::exists()),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Billing',
            'Expenses',
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'pending' => Tab::make('Pending')
                ->badge(Expense::query()->where('status', 'pending')->count())
                ->badgeColor(Status::Pending->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),
            'paid' => Tab::make('Paid')
                ->badge(Expense::query()->where('status', 'paid')->count())
                ->badgeColor(Status::Paid->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid')),
            'overdue' => Tab::make('Overdue')
                ->badge(Expense::query()->where('status', 'overdue')->count())
                ->badgeColor(Status::Overdue->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'overdue')),
            'cancelled' => Tab::make('Cancelled')
                ->badge(Expense::query()->where('status', 'cancelled')->count())
                ->badgeColor(Status::Cancelled->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}
