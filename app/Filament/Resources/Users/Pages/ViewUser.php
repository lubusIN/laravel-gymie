<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'User ' . $this->record->name;
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
            'Administration',
            UserResource::getUrl('index')   => 'Users',
            $this->record->name,
        ];
    }
}
