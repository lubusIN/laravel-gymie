<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->modalHeading('New service')
                ->modalWidth('sm')
                ->createAnother(false)
                ->visible(Service::exists()),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Memberships',
            'Services',
        ];
    }
}
