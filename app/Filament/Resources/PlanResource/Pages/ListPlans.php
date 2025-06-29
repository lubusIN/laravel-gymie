<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Enums\Status;
use App\Filament\Resources\PlanResource;
use App\Models\Plan;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPlans extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->modalAlignment('center')
                ->modalWidth('xl')
                ->modalHeading('New plan')
                ->createAnother(false)
                ->visible(Plan::exists()),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Memberships',
            'Plans',
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'active' => Tab::make('Active')
                ->badge(Plan::query()->where('status', 'active')->count())
                ->badgeColor(Status::Active->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active')),
            'inactive' => Tab::make('Inactive')
                ->badge(Plan::query()->where('status', 'inactive')->count())
                ->badgeColor(Status::Inactive->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'inactive')),
        ];
    }
}
