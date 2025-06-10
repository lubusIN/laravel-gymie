<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPlan extends ViewRecord
{
    protected static string $resource = PlanResource::class;

    public function getTitle(): string
    {
        return 'View ' . $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
