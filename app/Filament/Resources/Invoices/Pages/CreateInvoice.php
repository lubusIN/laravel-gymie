<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    public function getTitle(): string
    {
        return 'New Invoice';
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Billing',
            InvoiceResource::getUrl('index')   => 'Invoices',
        ];
    }
}
