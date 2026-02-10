<?php

namespace App\Filament\Resources\Members\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\Members\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit ' . $this->record->name;
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
