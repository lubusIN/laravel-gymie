<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'New Subscription';
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Memberships',
            SubscriptionResource::getUrl('index')   => 'Subscriptions',
        ];
    }
}
