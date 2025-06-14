<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->visible(Invoice::exists()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'issued' => Tab::make('Issued')
                ->badge(Invoice::query()->where('status', 'issued')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'issued')),
            'partial' => Tab::make('Partially Paid')
                ->badge(Invoice::query()->where('status', 'partial')->count())
                ->badgeColor('info')
                ->label('Partial')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'partial')),
            'overdue' => Tab::make('Overdue')
                ->badge(Invoice::query()->where('status', 'overdue')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'overdue')),
            'paid' => Tab::make('Paid')
                ->badge(Invoice::query()->where('status', 'paid')->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'paid')),
            'refund' => Tab::make('Refund')
                ->badge(Invoice::query()->where('status', 'refund')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'refund')),
            'cancelled' => Tab::make('Cancelled')
                ->badge(Invoice::query()->where('status', 'cancelled')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}
