<?php

namespace App\Filament\Resources\Invoices\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    public function getTitle(): string
    {
        return 'Edit Invoice No. #' . $this->record->number;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
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
