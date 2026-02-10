<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    public function getTitle(): string
    {
        return 'Edit ' . $this->record->member->name;
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
            'Memberships',
            SubscriptionResource::getUrl('index')   => 'Subscriptions',
            $this->record->member->name
        ];
    }
}
