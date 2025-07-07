<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
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
