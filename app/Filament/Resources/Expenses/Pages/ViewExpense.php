<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use Filament\Resources\Pages\ViewRecord;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    public function getTitle(): string
    {
        return 'Expense';
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Billing',
            ExpenseResource::getUrl('index') => 'Expenses',
            'View',
        ];
    }
}

