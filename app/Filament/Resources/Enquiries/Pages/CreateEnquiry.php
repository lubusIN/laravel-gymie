<?php

namespace App\Filament\Resources\Enquiries\Pages;

use App\Filament\Resources\Enquiries\EnquiryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEnquiry extends CreateRecord
{
    protected static string $resource = EnquiryResource::class;

    public function getTitle(): string
    {
        return 'New Enquiry';
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Sales',
            EnquiryResource::getUrl('index')   => 'Enquiries',
        ];
    }
}
