<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Enums\Status;
use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Models\Subscription;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(Subscription::exists()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'upcoming' => Tab::make('Upcoming')
                ->badge(Subscription::query()->where('status', 'upcoming')->count())
                ->badgeColor(Status::Upcoming->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'upcoming')),
            'ongoing' => Tab::make('Ongoing')
                ->badge(Subscription::query()->where('status', 'ongoing')->count())
                ->badgeColor(Status::Ongoing->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'ongoing')),
            'expiring' => Tab::make('Expiring')
                ->badge(Subscription::query()->where('status', 'expiring')->count())
                ->badgeColor(Status::Expiring->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'expiring')),
            'expired' => Tab::make('Expired')
                ->badge(Subscription::query()->where('status', 'expired')->count())
                ->badgeColor(Status::Expired->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'expired')),
            'renewed' => Tab::make('Renewed')
                ->badge(Subscription::query()->where('status', 'renewed')->count())
                ->badgeColor(Status::Renewed->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'renewed')),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Memberships',
            SubscriptionResource::getUrl('index')   => 'Subscriptions',
        ];
    }
}
