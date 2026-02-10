<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function getTitle(): string
    {
        return 'Invoice No. #' . $this->record->number;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Billing',
            InvoiceResource::getUrl('index')   => 'Invoices',
            $this->record->number
        ];
    }
}
