<?php

namespace App\Filament\Resources\Invoices\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Enums\Status;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(Invoice::exists()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'issued' => Tab::make('Issued')
                ->badge(Invoice::query()->where('status', 'issued')->count())
                ->badgeColor(Status::Issued->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'issued')),
            'partial' => Tab::make('Partially Paid')
                ->badge(Invoice::query()->where('status', 'partial')->count())
                ->badgeColor(Status::Partial->getColor())
                ->label('Partial')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'partial')),
            'overdue' => Tab::make('Overdue')
                ->badge(Invoice::query()->where('status', 'overdue')->count())
                ->badgeColor(Status::Overdue->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'overdue')),
            'paid' => Tab::make('Paid')
                ->badge(Invoice::query()->where('status', 'paid')->count())
                ->badgeColor(Status::Paid->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid')),
            'refund' => Tab::make('Refund')
                ->badge(Invoice::query()->where('status', 'refund')->count())
                ->badgeColor(Status::Refund->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'refund')),
            'cancelled' => Tab::make('Cancelled')
                ->badge(Invoice::query()->where('status', 'cancelled')->count())
                ->badgeColor(Status::Cancelled->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled')),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Billing',
            InvoiceResource::getUrl('index')   => 'Invoices',
        ];
    }
}
