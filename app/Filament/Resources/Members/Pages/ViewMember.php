<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
        ];
    }

    public function getTitle(): string
    {
        return 'Member ' . $this->record->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Memberships',
            MemberResource::getUrl('index')   => 'Members',
            $this->record->name
        ];
    }
}
