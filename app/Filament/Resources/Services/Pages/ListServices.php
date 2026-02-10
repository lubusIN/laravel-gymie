<?php

namespace App\Filament\Resources\Services\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
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
