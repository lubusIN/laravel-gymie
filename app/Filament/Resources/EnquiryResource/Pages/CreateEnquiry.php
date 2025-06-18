<?php

namespace App\Filament\Resources\EnquiryResource\Pages;

use App\Filament\Resources\EnquiryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEnquiry extends CreateRecord
{
    protected static string $resource = EnquiryResource::class;

    public function getTitle(): string
    {
        return 'New enquiry';
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Sales',
            EnquiryResource::getUrl('index')   => 'Enquiries',
        ];
    }
}
